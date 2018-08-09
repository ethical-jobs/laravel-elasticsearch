# v2.1.3

- Interface with incorrect namespace

# v2.1.2

- Simplified repository constructor requirements

# v2.1.1

- Dependancy updates

# v2.1.0

- Refactor: es client builder
- Refactor: test cases

# v2.0.0

- Rewrite: queued indexing
- Rewrite: repository layer
- Rewrite: testing
- Removed slack logging
- Configuration refactor

# v1.6.1

- Lock file updates for dependancies

# v1.6.0

- Refactoring test helpers

# v1.5.3

- Force delete now deletes soft-deletable models from the index as it should have.

# v1.5.2

- Removed logging for single document indexing, too noisy.

# v1.5.1

- Swallowing and logging indexing obervables

# v1.5.0

- Reducing slack logging calls
- Queing at the indexable level, not a user defined process level
- Small logging changes

# v1.4.8

- Indexing command lock

# v1.4.7

- Bug fixes
- Test suite refactor and extension

# v1.4.1

- Adding elasticsearch repositories
- Repository tests

# v1.3.10

- Async query indexing fixes and refactors
- Serializable IndexQueries

# v1.3.0

- Added async indexing feature
- Added slack indexing logger

# v1.2.2

- Removing default settings and mappings from config
- Reduced indexing chunk-size to 300

# v1.2.0

- Updated observers to not fire additional non-needed events / listeners
- Extended testing aaround observers and indexing

# v1.0.0

- Initial version