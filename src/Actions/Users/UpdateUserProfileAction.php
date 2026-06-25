<?php

namespace App\Actions\Users;

use App\Actions\BAction;
use App\DTOs\UserDto;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Repositories\RolesRepository;
use App\Repositories\UsersRepository;
use App\Services\CloudinaryService;
use App\Services\PermissionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function password_verify;
use function password_hash;
use const PASSWORD_DEFAULT;

class UpdateUserProfileAction extends BAction {
    public function __construct(
        PermissionService                $permission_service,
        private readonly UsersRepository $repo,
        private readonly RolesRepository $role_repo,
        private readonly CloudinaryService $cloudinary_service
    ) {
        parent::__construct($permission_service);
    }

    protected function handle(Request $request, Response $response, array $args): Response {
        $userId = $request->getAttribute('user_id');
        
        $user = $this->repo->findById($userId);
        if (!$user) {
            throw new NotFoundException('Account not found!');
        }

        $parsedBody = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        $updated = false;

        // Case 1: Avatar upload
        if (isset($uploadedFiles['avatar'])) {
            $avatarFile = $uploadedFiles['avatar'];
            if ($avatarFile->getError() === UPLOAD_ERR_OK) {
                // Upload new avatar to Cloudinary
                $uploadResult = $this->cloudinary_service->uploadUploadedFile($avatarFile, 'VBlog_user_avatar');
                
                // Get old public id to delete it
                $oldPublicId = $user['avatar_public_id'] ?? null;
                
                // Save new avatar path and public id to database
                $this->repo->updateAvatar($userId, $uploadResult['url'], $uploadResult['public_id']);
                
                // Delete old avatar from Cloudinary if existed
                if ($oldPublicId) {
                    try {
                        $this->cloudinary_service->deleteImage($oldPublicId);
                    } catch (\Exception $e) {
                        // Ignore or log old avatar delete errors
                    }
                }
                
                $updated = true;
            }
        }

        // Case 2: Change password
        if (!empty($parsedBody['currentPassword']) && !empty($parsedBody['newPassword'])) {
            $currentPassword = $parsedBody['currentPassword'];
            $newPassword = $parsedBody['newPassword'];

            // Verify current password
            if (!password_verify($currentPassword, $user['password_hash'])) {
                throw new ValidationException('Your current password is incorrect!');
            }

            // Check if new password is same as current password
            if ($currentPassword === $newPassword) {
                throw new ValidationException('Your new password must be different from current password!');
            }

            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->repo->updatePassword($userId, $newPasswordHash);
            $updated = true;
        }

        // Case 3: Change username
        if (!empty($parsedBody['username'])) {
            $username = trim($parsedBody['username']);
            $currentUsername = $user['user_name'] ?? '';

            if ($username === $currentUsername) {
                throw new ValidationException('Your new username must be different from current username!');
            }

            $this->repo->updateUsername($userId, $username);
            $updated = true;
        }

        if (!$updated) {
            throw new ValidationException('No information was updated!');
        }

        // Retrieve fresh user details and roles to return
        $updatedUser = $this->repo->findById($userId);
        $role = $this->role_repo->findRoleCodeByUserId($userId);

        $userDto = UserDto::fromArray($updatedUser, $role);

        return $this->json($response, [
            'message' => 'Your account has been updated successfully!',
            'user' => $userDto
        ]);
    }
}
