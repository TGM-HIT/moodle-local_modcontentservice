MOODLE_DOCKER := "../moodle-docker"
MOODLE_ROOT := "../moodle"
# MOODLE_VERSION := "v4.5.5"
MOODLE_VERSION := "v5.0.1"

export MOODLE_DOCKER_WWWROOT := MOODLE_ROOT
export MOODLE_DOCKER_DB := "pgsql"

get-moodle:
	test -d "{{MOODLE_ROOT}}" || \
		git clone --depth 1 -b "{{MOODLE_VERSION}}" git://git.moodle.org/moodle.git "{{MOODLE_ROOT}}"
	cd "{{MOODLE_ROOT}}" && \
		git show-ref --tags "{{MOODLE_VERSION}}" --quiet || \
		(git fetch --depth=1 origin "{{MOODLE_VERSION}}" && \
		git tag -a -m "{{MOODLE_VERSION}}" "{{MOODLE_VERSION}}" "FETCH_HEAD^{}")
	cd "{{MOODLE_ROOT}}" && git checkout "{{MOODLE_VERSION}}"

cp-config-php:
	cp "{{MOODLE_DOCKER}}/config.docker-template.php" "{{MOODLE_ROOT}}/config.php"

copy-plugin NAME:
	cp -r "{{NAME}}" "{{MOODLE_ROOT}}/local/{{NAME}}"
remove-plugin NAME:
	rm -r "{{MOODLE_ROOT}}/local/{{NAME}}"

copy-plugins: (copy-plugin "resourceservice")
remove-plugins: (remove-plugin "resourceservice")

compose *ARGS:
	"{{MOODLE_DOCKER}}/bin/moodle-docker-compose" {{ARGS}}

wait-for-db:
	"{{MOODLE_DOCKER}}/bin/moodle-docker-wait-for-db"

init: (compose "exec" "webserver" "php" "admin/cli/install_database.php"
	"--agree-license" "--fullname='Docker moodle'" "--shortname='docker_moodle'"
	"--summary='Docker moodle site'" "--adminpass='test'" "--adminemail='admin@example.com'")

up: (compose "up" "-d") wait-for-db

down: (compose "down")
