<?php
#
# Default NeechySystem Page
# This gets saved when install script is run
#

$t = $this;   # templater object

?>
# NeechyFormatting

Neechy uses extended [markdown syntax](http://en.wikipedia.org/wiki/Markdown) and extends it further with a few little twists of its own.

Here are some of the more useful features of the syntax:


## Headers

### Markdown:

    # Neechy Formatting

    ## Headers

    ### Markdown:

### Produces:

The headers you see above.


## Links

### Markdown:

    [github](http://github.com/)

    <http://github.com/>

    [[http://github.com github.com]] (WikkaWiki syntax coming soon!)

    NeechyFormatting (WikkaWiki syntax coming soon!)

### Produces:

[github](http://github.com/)

<http://github.com/>

[[http://github.com github.com]] (WikkaWiki syntax coming soon!)

NeechyFormatting (WikkaWiki syntax coming soon!)


## Code

### Markdown:
    Inline `code` is produced using backticks

        # Text indented four times
        # renders as preformatted
        # code blocks

    ```
    # Neechy also supports
    # fenced code blocks
    ```

### Produces:
Inline `code` is produced using backticks

    # Text indented four times
    # renders as preformatted
    # code blocks

```
# Neechy also supports
# fenced code blocks
```


## Text Formatting

### Markdown:
    *Italics* are produced like *this* or _this_
    **Bold text** is produced like **this** or __this__
    And if you need to ~~strike something through~~

### Produces:
*Italics* are produced like *this* or _this_
**Bold text** is produced like **this** or __this__
And if you need to ~~strike something through~~


## Tables

### Markdown:
    | Item      | Value | Qty |
    | --------- | -----:|:--: |
    | Computer  | $1600 | 5   |
    | Phone     |   $12 | 12  |
    | Pipe      |    $1 |234  |

### Produces:
| Item      | Value | Qty |
| --------- | -----:|:--: |
| Computer  | $1600 | 5   |
| Phone     |   $12 | 12  |
| Pipe      |    $1 |234  |


## More Information
For more information on markdown syntax, see this [StackOverflow page](http://stackoverflow.com/editing-help).
