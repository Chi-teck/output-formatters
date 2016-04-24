<?php
namespace Consolidation\OutputFormatters;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Consolidation\OutputFormatters\StructuredData\AssociativeList;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class FormattersTests extends \PHPUnit_Framework_TestCase
{
    protected $formatterManager;

    function setup() {
        $this->formatterManager = new FormatterManager();
        //$this->output = new BufferedOutput();
        //$this->output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        //$this->logger = new Logger($this->output);
    }

    function assertFormattedOutputMatches($expected, $format, $data, $configurationData = [], $options = []) {
        $output = new BufferedOutput();
        $this->formatterManager->write($output, $format, $data, $configurationData, $options);
        $actual = preg_replace('#[ \t]*$#sm', '', $output->fetch());
        $this->assertEquals(rtrim($expected), rtrim($actual));
    }

    function testSimpleYaml()
    {
        $data = [
            'one' => 'a',
            'two' => 'b',
            'three' => 'c',
        ];

        $expected = <<<EOT
one: a
two: b
three: c
EOT;

        $this->assertFormattedOutputMatches($expected, 'yaml', $data);
    }

    function testNestedYaml()
    {
        $data = [
            'one' => [
                'i' => ['a', 'b', 'c'],
            ],
            'two' => [
                'ii' => ['q', 'r', 's'],
            ],
            'three' => [
                'iii' => ['t', 'u', 'v'],
            ],
        ];

        $expected = <<<EOT
one:
  i:
    - a
    - b
    - c
two:
  ii:
    - q
    - r
    - s
three:
  iii:
    - t
    - u
    - v
EOT;

        $this->assertFormattedOutputMatches($expected, 'yaml', $data);
    }

    function testSimpleJson()
    {
        $data = [
            'one' => 'a',
            'two' => 'b',
            'three' => 'c',
        ];

        $expected = <<<EOT
{
    "one": "a",
    "two": "b",
    "three": "c"
}
EOT;

        $this->assertFormattedOutputMatches($expected, 'json', $data);
    }

    function testNestedJson()
    {
        $data = [
            'one' => [
                'i' => ['a', 'b', 'c'],
            ],
            'two' => [
                'ii' => ['q', 'r', 's'],
            ],
            'three' => [
                'iii' => ['t', 'u', 'v'],
            ],
        ];

        $expected = <<<EOT
{
    "one": {
        "i": [
            "a",
            "b",
            "c"
        ]
    },
    "two": {
        "ii": [
            "q",
            "r",
            "s"
        ]
    },
    "three": {
        "iii": [
            "t",
            "u",
            "v"
        ]
    }
}
EOT;

        $this->assertFormattedOutputMatches($expected, 'json', $data);
    }

    function testSimplePrintR()
    {
        $data = [
            'one' => 'a',
            'two' => 'b',
            'three' => 'c',
        ];

        $expected = <<<EOT
Array
(
    [one] => a
    [two] => b
    [three] => c
)
EOT;

        $this->assertFormattedOutputMatches($expected, 'print-r', $data);
    }

    function testNestedPrintR()
    {
        $data = [
            'one' => [
                'i' => ['a', 'b', 'c'],
            ],
            'two' => [
                'ii' => ['q', 'r', 's'],
            ],
            'three' => [
                'iii' => ['t', 'u', 'v'],
            ],
        ];

        $expected = <<<EOT
Array
(
    [one] => Array
        (
            [i] => Array
                (
                    [0] => a
                    [1] => b
                    [2] => c
                )

        )

    [two] => Array
        (
            [ii] => Array
                (
                    [0] => q
                    [1] => r
                    [2] => s
                )

        )

    [three] => Array
        (
            [iii] => Array
                (
                    [0] => t
                    [1] => u
                    [2] => v
                )

        )

)
EOT;

        $this->assertFormattedOutputMatches($expected, 'print-r', $data);
    }

    function testSimpleVarExport()
    {
        $data = [
            'one' => 'a',
            'two' => 'b',
            'three' => 'c',
        ];

        $expected = <<<EOT
array (
  'one' => 'a',
  'two' => 'b',
  'three' => 'c',
)
EOT;

        $this->assertFormattedOutputMatches($expected, 'var_export', $data);
    }

    function testNestedVarExport()
    {
        $data = [
            'one' => [
                'i' => ['a', 'b', 'c'],
            ],
            'two' => [
                'ii' => ['q', 'r', 's'],
            ],
            'three' => [
                'iii' => ['t', 'u', 'v'],
            ],
        ];

        $expected = <<<EOT
array (
  'one' =>
  array (
    'i' =>
    array (
      0 => 'a',
      1 => 'b',
      2 => 'c',
    ),
  ),
  'two' =>
  array (
    'ii' =>
    array (
      0 => 'q',
      1 => 'r',
      2 => 's',
    ),
  ),
  'three' =>
  array (
    'iii' =>
    array (
      0 => 't',
      1 => 'u',
      2 => 'v',
    ),
  ),
)
EOT;

        $this->assertFormattedOutputMatches($expected, 'var_export', $data);
    }

    function testList()
    {
        $data = [
            'one' => 'a',
            'two' => 'b',
            'three' => 'c',
        ];

        $expected = <<<EOT
a
b
c
EOT;

        $this->assertFormattedOutputMatches($expected, 'list', $data);
    }

    function testSimpleCsv()
    {
        $data = ['a', 'b', 'c'];
        $expected = "a,b,c";

        $this->assertFormattedOutputMatches($expected, 'csv', $data);
    }

    function testLinesOfCsv()
    {
        $data = [['a', 'b', 'c'], ['x', 'y', 'z']];
        $expected = "a,b,c\nx,y,z";

        $this->assertFormattedOutputMatches($expected, 'csv', $data);
    }

    function testCsvWithEscapedValues()
    {
        $data = ["Red apple", "Yellow lemon"];
        $expected = '"Red apple","Yellow lemon"';

        $this->assertFormattedOutputMatches($expected, 'csv', $data);
    }

    function testCsvWithEmbeddedSingleQuote()
    {
        $data = ["John's book", "Mary's laptop"];
        $expected = <<<EOT
"John's book","Mary's laptop"
EOT;

        $this->assertFormattedOutputMatches($expected, 'csv', $data);
    }

    function testCsvWithEmbeddedDoubleQuote()
    {
        $data = ['The "best" solution'];
        $expected = <<<EOT
"The ""best"" solution"
EOT;

        $this->assertFormattedOutputMatches($expected, 'csv', $data);
    }

    function testNoFormatterSelected()
    {
        $data = 'Hello';
        $expected = $data;
        $this->assertFormattedOutputMatches($expected, '', $data);
    }

    function testCsvBothKindsOfQuotes()
    {
        $data = ["John's \"new\" book", "Mary's \"modified\" laptop"];
        $expected = <<<EOT
"John's ""new"" book","Mary's ""modified"" laptop"
EOT;

        $this->assertFormattedOutputMatches($expected, 'csv', $data);
    }

    protected function simpleTableExampleData()
    {
        $data = [
            [
                'one' => 'a',
                'two' => 'b',
                'three' => 'c',
            ],
            [
                'one' => 'x',
                'two' => 'y',
                'three' => 'z',
            ],
        ];
        return new RowsOfFields($data);
    }

    /**
     * @expectedException \Consolidation\OutputFormatters\Exception\IncompatibleDataException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\AssociativeList. Instead, an array was provided.
     */
    function testIncompatibleDataForTableFormatter()
    {
        $data = $this->simpleTableExampleData();
        $this->assertFormattedOutputMatches('Should throw an exception before comparing the table data', 'table', $data->getArrayCopy());
    }

    function testSimpleTable()
    {
        $data = $this->simpleTableExampleData();

        $expected = <<<EOT
+-----+-----+-------+
| One | Two | Three |
+-----+-----+-------+
| a   | b   | c     |
| x   | y   | z     |
+-----+-----+-------+
EOT;

        $expectedJson = <<<EOT
[
    {
        "one": "a",
        "two": "b",
        "three": "c"
    },
    {
        "one": "x",
        "two": "y",
        "three": "z"
    }
]
EOT;

        $this->assertFormattedOutputMatches($expected, 'table', $data);
        $this->assertFormattedOutputMatches($expectedJson, 'json', $data);
    }

    function testSimpleTableWithFieldLabels()
    {
        $data = $this->simpleTableExampleData();

        $expected = <<<EOT
+------+----+-----+
| Ichi | Ni | San |
+------+----+-----+
| a    | b  | c   |
| x    | y  | z   |
+------+----+-----+
EOT;

        $expectedWithReorderedFields = <<<EOT
+-----+------+
| San | Ichi |
+-----+------+
| c   | a    |
| z   | x    |
+-----+------+
EOT;

        $expectedJson = <<<EOT
[
    {
        "three": "c",
        "one": "a"
    },
    {
        "three": "z",
        "one": "x"
    }
]
EOT;

        $configurationData = [
            'field-labels' => ['one' => 'Ichi', 'two' => 'Ni', 'three' => 'San'],
        ];
        $this->assertFormattedOutputMatches($expected, 'table', $data, $configurationData);
        $this->assertFormattedOutputMatches($expectedWithReorderedFields, 'table', $data, $configurationData, ['fields' => ['three', 'one']]);
        $this->assertFormattedOutputMatches($expectedWithReorderedFields, 'table', $data, $configurationData, ['fields' => ['San', 'Ichi']]);
        $this->assertFormattedOutputMatches($expectedJson, 'json', $data, $configurationData, ['fields' => ['San', 'Ichi']]);
    }

    protected function simpleListExampleData()
    {
        $data = [
            'one' => 'apple',
            'two' => 'banana',
            'three' => 'carrot',
        ];
        return new AssociativeList($data);
    }

    /**
     * @expectedException \Consolidation\OutputFormatters\Exception\IncompatibleDataException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Data provided to Consolidation\OutputFormatters\Formatters\TableFormatter must be either an instance of Consolidation\OutputFormatters\StructuredData\RowsOfFields or an instance of Consolidation\OutputFormatters\StructuredData\AssociativeList. Instead, an array was provided.
     */
    function testIncompatibleListDataForTableFormatter()
    {
        $data = $this->simpleListExampleData();
        $this->assertFormattedOutputMatches('Should throw an exception before comparing the table data', 'table', $data->getArrayCopy());
    }

    function testSimpleList()
    {
        $data = $this->simpleListExampleData();

        $expected = <<<EOT
+-------+--------+
| One   | apple  |
| Two   | banana |
| Three | carrot |
+-------+--------+
EOT;

        $this->assertFormattedOutputMatches($expected, 'table', $data);
    }

    function testSimpleListWithFieldLabels()
    {
        $data = $this->simpleListExampleData();

        $expected = <<<EOT
+------+--------+
| Ichi | apple  |
| Ni   | banana |
| San  | carrot |
+------+--------+
EOT;

        $expectedWithReorderedFields = <<<EOT
+------+--------+
| San  | carrot |
| Ichi | apple  |
+------+--------+
EOT;

        $expectedJson = <<<EOT
[
    {
        "three": "carrot",
        "one": "apple"
    }
]
EOT;


        $configurationData = [
            'field-labels' => ['one' => 'Ichi', 'two' => 'Ni', 'three' => 'San'],
        ];
        $this->assertFormattedOutputMatches($expected, 'table', $data, $configurationData);
        $this->assertFormattedOutputMatches($expectedWithReorderedFields, 'table', $data, $configurationData, ['fields' => ['three', 'one']]);
        $this->assertFormattedOutputMatches($expectedWithReorderedFields, 'table', $data, $configurationData, ['fields' => ['San', 'Ichi']]);
        $this->assertFormattedOutputMatches($expectedJson, 'json', $data, $configurationData, ['fields' => ['San', 'Ichi']]);
    }

}

