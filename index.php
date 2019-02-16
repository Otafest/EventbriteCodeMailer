<?php
    require_once('EbFunctions.php');

    //Let's grab our ticket types from Eventbrite and throw them in a select box
    $tickets = GetTicketTypes();
    $options = "";
    foreach ($tickets as $i => $v)
    {
        $options .= sprintf("<option value='%s'>%s</option>", $v[1], $v[0]);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Eventbrite Ticket Generator</title>
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=font1|font2|etc" type="text/css">
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css"
            integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w"
            crossorigin="anonymous">
        <!--[if lte IE 8]>
            <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-old-ie-min.css">
        <![endif]-->
        <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
        <!--<![endif]-->
    </head>
    <body>
        <div class="pure-g">
            <div class="pure-u-1-4"></div>
            <div class="pure-u-1-2">
                <form class="pure-form pure-form-aligned">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="Emails">E-Mail(s)</label>
                            <input id="Emails" type="text" required>
                            <span class="pure-form-message-inline">Semi-colon separated list.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="TicketType">Ticket Type</label>
                            <select id="TicketType" >
                                <?PHP echo $options; ?>
                            </select>
                        </div>
                        <div class="pure-control-group">
                            <label for="Discount">% Discount</label>
                            <input id="Discount" type="number" min=0 max=100 value=100>
                        </div>
                        <div class="pure-controls">
                             <button type="submit" class="pure-button pure-button-primary">Submit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="pure-u-1-4"></div>
        </div>
    </body>
</html>