source_dir = src/
tester = vendor/bin/tester
tests_dir = tests/
coverage_name = coverage.html

all:

$(tester):
	composer update --dev

test: $(tester)
	php $(tester) $(tests_dir)

coverage: $(tester)
	$(tester) $(tests_dir) -c $(tests_dir)php-unix.ini --coverage $(coverage_name) --coverage-src $(source_dir)

clean:
	@rm -f $(coverage_name)

.PHONY: test coverage clean
