<?php 
$gpio = '/usr/local/bin/gpio';
shell_exec("$gpio -g mode 17 out");
shell_exec("$gpio -g mode 23 out");

function isPinOn($pin){
    global $gpio;
    $pin = (int) filter_var($pin, FILTER_VALIDATE_INT);
    return filter_var(shell_exec("$gpio -g read $pin"), FILTER_VALIDATE_BOOLEAN);
};

function setPinOut($pin, $val){
    global $gpio;
    shell_exec("$gpio -g write $pin $val");
};

if(isset($_POST['pin'])):
    $pin = filter_var($_POST['pin'], FILTER_VALIDATE_INT);

    if(isset($_POST['on'])){
        $val = (int) filter_var($_POST['on'], FILTER_VALIDATE_BOOLEAN);

        setPinOut($pin,  $val);

        $pinOn = isPinOn($pin);
        if($pinOn != $val){
            http_response_code(900);
        }
        
        echo json_encode(['pin' => $pin, 'on' => $pinOn]);
    }else{
        echo json_encode(['pin' => $pin, 'on' => isPinOn($pin)]);
    }

else: ?>
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>Kamerasteuerung</title>
            <script src="https://cdn.jsdelivr.net/npm/fetch-polyfill@0.8.2/fetch.min.js"></script>
            <link rel="stylesheet" href="style.css"/>
        </head>
        <body>
            <header>
                <h2>Kamerasteuerung</h2>
            </header>
            <div id="switches">
            </div>
            <script src="script.js"></script>
        </body>
</html>
<?php endif; ?>