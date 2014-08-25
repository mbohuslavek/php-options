source_dir = src/
tester = vendor/bin/tester
tests_dir = tests/
coverage_name = coverage.html

test:
	php $(tester) tests/

coverage:
	$(tester) $(tests_dir) -c $(tests_dir)php-unix.ini --coverage $(coverage_name) --coverage-src $(source_dir)

clean:
	@rm -f $(coverage_name)

.PHONY: test coverage clean
