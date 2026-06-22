ENV ?= local
PHINX = vendor/bin/phinx

.PHONY: re-migrate migrate seed create rollback run

run:
	@php -S localhost:8080 -t public

migrate:
	@echo "Migrating..."
	@$(PHINX) migrate -e $(ENV)

seed:
	@echo "Seeding..."
	@$(PHINX) seed:run -e $(ENV)

rollback:
	@echo "Rollback..."
	@$(PHINX) rollback -e $(ENV) -t 0

create:
	@[ -n "$(name)" ] || exit 1
	@[ "$(type)" = "migration" ] && $(PHINX) create $(name) || \
	([ "$(type)" = "seeder" ] && $(PHINX) seed:create $(name) || exit 1)

re-migrate: rollback migrate seed
