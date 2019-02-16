<?php
    require_once('EbFunctions.php');
    require_once('Email.php');
    require_once('config.php');

    //Let's grab our ticket types from Eventbrite and throw them in a select box
    $tickets = GetTicketTypes();
    $options = "";
    foreach ($tickets as $i => $v)
    {
        $options .= sprintf("<option value='%s'>%s</option>", $v[1], $v[0]);
    }
?>

<?php
    //Form submission stuff
    if(isset($_POST) && $_POST['submit'] == "Submit")
    {
        $TrimmedEmails = str_replace(' ', '', $_POST['Emails']);
        $EmailList = explode(";", $TrimmedEmails);
        
        foreach ($EmailList as $i => $email)
        {
            $TicketCode = GenerateDiscountCode($_POST['TicketType'], $_POST['Discount']);

            if($TicketCode)
                SendEmail($email, $_POST['EmailSubject'], $_POST['EmailBody'], $TicketCode);
            else
                echo sprintf("Couldn't generate a code for %s.<br />\n", $email);
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Eventbrite Discount Code Generator</title>
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
                <form class="pure-form pure-form-aligned" Name="DiscountForm" Method="POST">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="Emails">E-Mail(s)</label>
                            <input id="Emails" name="Emails" type="text" required>
                            <span class="pure-form-message-inline">Semi-colon separated list.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="EmailSubject">E-Mail Subject</label>
                            <input id="EmailSubject" name="EmailSubject" class="pure-input-1-2" type="text" value="<?PHP echo MAIL_DEFAULT_SUBJECT; ?>"required>
                        </div>
                        <div class="pure-control-group">
                            <label for="EmailBody">E-Mail Body</label>
                            <textarea id="EmailBody" name="EmailBody" class="pure-input-1-2" required><?PHP echo MAIL_DEFAULT_BODY; ?></textarea>
                        </div>
                        <div class="pure-control-group">
                            <label for="TicketType">Ticket Type</label>
                            <select id="TicketType" name="TicketType" class="pure-input-1-2">
                                <?PHP echo $options; ?>
                            </select>
                        </div>
                        <div class="pure-control-group">
                            <label for="Discount">% Discount</label>
                            <input id="Discount" name="Discount" type="number" min=0 max=100 value=100>
                        </div>
                        <div class="pure-controls">
                             <input type="submit" class="pure-button pure-button-primary" name="submit" value="Submit" />
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="pure-u-1-4"></div>
        </div>
    </body>
</html>