---
title: Integration and Unit Tests
permalink: '/tests/backend/'
---

See the [CakePHP documentation](https://book.cakephp.org/4/en/development/testing.html)
for a general introduction to testing in CakePHP applications.
See the file `tests/TestCase/AppTestCase.php` for extensions that Epigraf implements,
e.g. for comparing HTML output and for authenticating users.

**Integration tests** simulate requests by GET, POST and DELETE requests to controller actions
using PHPUnit.

A GET request corresponds to entering a URL in the browser or clicking a menu item in the application.
In the context of Epigraf, those requests typically call the `index` and `view` actions of a controller.
POST requests are used to add or modify data, such as when text is entered into input elements.
In Epigraf, such requests are usually handled in the `edit` and `add` actions of the controller.
DELETE requests are used to remove data by calling the `delete` actions.

Some operations comprise a series of requests simulated in the tests.
For example, when deleting a record, a confirmation page is delivered by a GET request,
during which the user is prompted to confirm the deletion of the data record.
Subsequently, a DELETE request is executed, resulting in the removal of the data record.
Finally, the controller redirects to the index method using a GET request.

**Unit tests** assert the output of specific functions on the model or view layer.
For example, model tests are employed to ascertain that reading from and writing to the database
produces the expected results.


## Create Tests

Tests are located in subfolders of `tests/TestCase` for the application and in each plugin folder.
Usually each controller, model or view class has a corresponding test class.
Within the test class, each single test scenario is defined in a method prefixed with `test`.
For example, index action of the `DatabanksController` class is tested in `Controller/DatabanksControllerTest`.

```
public function testIndex()
{
    $this->loginUser('admin');
    $this->get("databanks/index");
    $this->assertHtmlEqualsComparison();
}
```

As most actions are not permitted for unauthenticated users,
you first need to simulate the login process. Then a request can be simulated
and the result can be compared to the expected output.

Testing one of the MVC layers instead of the whole stack, does not require simulating the login process.
Instead, the method to be tested is called directly, and the output is compared to the reference data:

```
    public function testFindContainAncestors()
    {
        $properties = $this->Properties
            ->find('containAncestors')
            ->where(['Properties.id' => 10226])
            ->toArray();

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }
```

In the test method, all classes and functions that are needed for the test must be loaded.
For making the the previous example work, a PropertiesTable class must be instantiated.
This is usually done in the `setUp` method of the test class:
```
$this->Properties = $this->fetchTable('Epi.Properties');
```


The reference data is stored within the directory `tests/Comparisons` under
the name of the test class and test method. Some tests require data to be posted or files to be uploaded to the endpoints.
Such test data ist stored in the directory `tests/Testdata`.

For developing new tests, it is recommended to use the existing tests as a blueprint.

## Update Tests

After existing functions have been customized, the output of Epigraf changes.
This causes the corresponding tests to fail, as the stored comparison data no longer corresponds
to the newly generated data. Since many comparison files contain complex HTML structures,
it is not always straightforward to change them manually.

Epigraf allows automatically updating comparison files by setting
the property `$overwriteComparison` in the root AppTestCase class to `true` before running a test.
Overwriting comparison files is extremely effective and should be used _very carefully_,
as it causes all tests to pass, even if they contain errors.
Make sure to set the property back to `false` after updating the reference snapshots.

After automatically updating the comparison files, check the changes before you commit them to the repository.

## Fixture Data

Running integration tests needs test databases with test data, so-called fixtures.
Epigraf uses both, CakePHP's fixture classes and SQL dumps to load test data.

Fixture classes are stored in the corresponding `Fixtures` folder of the plugin or the application. Each fixture
contains a table definition and records to be inserted into the test database. The test database
connection is configured in the `config/app.php` file.

Fixtures are usually loaded by providing the fixture names in the `$fixtures` variable of the test class:

```
public $fixtures = [
	'plugin.Epi.Properties',
	'plugin.Epi.TwoArticlesCompound'
];
```

Alternatively, they can be explicitly loaded in a test method:
```
public function testFindHasArticleOptions()
{
	$this->loadFixtures('TwoArticlesCompound');
	...
}
```

For future development it is recommended to rely on SQL dumps.
The SQL dumps can be easily created from real data and are more flexible than fixture classes
because they can include all dependencies - for example projects, articles, sections and items -
in the same file. SQL dumps are located in the folder `Testdata/Databases`.
You will find further notes on dump generation in the folder's readme file.

