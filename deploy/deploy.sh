# recreate tmp dir
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
echo $DIR

sudo rm -rf $DIR/../tmp
mkdir $DIR/../tmp
mkdir $DIR/../tmp/cache
mkdir $DIR/../tmp/cache/twig
mkdir $DIR/../tmp/cache/doctrine

chmod a+rw $DIR/../tmp -R

# or add writable to www-data only
chmod a+rw $DIR/../public/files -R


# clean unit test and code coverage reports
# sudo rm -rf ../tests/coverage
# sudo rm -rf ../tests/reports

# mkdir ../tests/coverage
# mkdir ../tests/reports

# chmod a+rw ../tests/coverage -R
# chmod a+rw ../tests/reports -R
