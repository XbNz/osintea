# Effects per Mutator

| Mutator                         | Mutations | Killed | Escaped | Errors | Syntax Errors | Timed Out | Skipped | Ignored | MSI (%s) | Covered MSI (%s) |
| ------------------------------- | --------- | ------ | ------- | ------ | ------------- | --------- | ------- | ------- | -------- | ---------------- |
| ArrayItem                       |        50 |     44 |       2 |      0 |             0 |         0 |       0 |       0 |    88.00 |            95.65 |
| ArrayItemRemoval                |        70 |     34 |      26 |      0 |             0 |         0 |       0 |       0 |    48.57 |            56.67 |
| ArrayOneItem                    |         2 |      2 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| Assignment                      |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| BitwiseOr                       |         2 |      1 |       1 |      0 |             0 |         0 |       0 |       0 |    50.00 |            50.00 |
| CastArray                       |         4 |      2 |       2 |      0 |             0 |         0 |       0 |       0 |    50.00 |            50.00 |
| CastFloat                       |         1 |      1 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| CastInt                         |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| Concat                          |        29 |     16 |      11 |      0 |             0 |         0 |       0 |       0 |    55.17 |            59.26 |
| ConcatOperandRemoval            |        46 |     30 |      12 |      0 |             0 |         0 |       0 |       0 |    65.22 |            71.43 |
| DecrementInteger                |        93 |     19 |      56 |      0 |             0 |         0 |       0 |       7 |    22.09 |            25.33 |
| Division                        |         3 |      3 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| Exponentiation                  |         1 |      1 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| FalseValue                      |        36 |     22 |       6 |      0 |             0 |         0 |       0 |       0 |    61.11 |            78.57 |
| Foreach_                        |        11 |      9 |       2 |      0 |             0 |         0 |       0 |       0 |    81.82 |            81.82 |
| FunctionCallRemoval             |         4 |      1 |       2 |      0 |             0 |         0 |       0 |       0 |    25.00 |            33.33 |
| GreaterThan                     |         1 |      1 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| GreaterThanNegotiation          |         1 |      1 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| GreaterThanOrEqualTo            |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| GreaterThanOrEqualToNegotiation |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| Identical                       |        47 |     39 |       1 |      0 |             0 |         0 |       0 |       0 |    82.98 |            97.50 |
| IfNegation                      |         2 |      2 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| Increment                       |         1 |      0 |       1 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| IncrementInteger                |        89 |     19 |      52 |      0 |             0 |         0 |       0 |       7 |    23.17 |            26.76 |
| LogicalNot                      |         1 |      0 |       1 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| LogicalOr                       |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| LogicalOrAllSubExprNegation     |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| LogicalOrNegation               |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| LogicalOrSingleSubExprNegation  |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| MBString                        |         3 |      0 |       3 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| MethodCallRemoval               |       191 |     91 |      39 |      1 |             0 |         0 |       0 |      38 |    60.13 |            70.23 |
| Minus                           |         1 |      1 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| Multiplication                  |         1 |      1 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |
| NotIdentical                    |        11 |      7 |       1 |      0 |             0 |         0 |       0 |       0 |    63.64 |            87.50 |
| NullSafeMethodCall              |         2 |      0 |       1 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| Plus                            |         1 |      0 |       1 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| PlusEqual                       |         1 |      0 |       0 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| Ternary                         |         3 |      2 |       1 |      0 |             0 |         0 |       0 |       0 |    66.67 |            66.67 |
| Throw_                          |        12 |      6 |       0 |      0 |             0 |         0 |       0 |       0 |    50.00 |           100.00 |
| TrueValue                       |        29 |     14 |      10 |      0 |             0 |         0 |       0 |       0 |    48.28 |            58.33 |
| UnwrapArrayFilter               |         4 |      3 |       0 |      0 |             0 |         0 |       0 |       0 |    75.00 |           100.00 |
| UnwrapArrayKeys                 |         1 |      0 |       1 |      0 |             0 |         0 |       0 |       0 |     0.00 |             0.00 |
| UnwrapArrayMap                  |         3 |      3 |       0 |      0 |             0 |         0 |       0 |       0 |   100.00 |           100.00 |