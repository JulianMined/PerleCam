<?php 
system("/usr/local/bin/gpio -g mode 17 out");
system("/usr/local/bin/gpio -g mode 23 out");

function isPinOn($pin){
    return filter_var(system("/usr/local/bin/gpio read $pin"), FILTER_VALIDATE_BOOLEAN);
};

function setPinOut($pin, $val){
    $val = (int) filter_var($val, FILTER_VALIDATE_BOOLEAN);
    $pin = (int) filter_var($pin, FILTER_VALIDATE_INT);

    system(escapeshellcmd("/usr/local/bin/gpio -g write $pin $val"));

    $pinOn = isPinOn($pin);
    if($pinOn != $pin){
        http_response_code(900);
    }

    return json_encode(['pin' => $pin, 'on' => $pinOn]);
};

if(isset($_POST['pin']) && isset($_POST['on'])):
    echo setPinOut($_POST['pin'], $_POST['on']);
else: ?>
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>Kamerasteuerung</title>
            <script src="https://cdn.jsdelivr.net/npm/fetch-polyfill@0.8.2/fetch.min.js"></script>
            <style>
                @font-face {
                    font-family: 'nokia';
                    src: url('nokiafc22.ttf') format('truetype');
                }
                body, html {
                    margin: 0;
                    width: 100%;
                    height: 100%;
                }
                body {
                    background: #8f97a8;
                    font-family: 'nokia';
                }
                header {
                    background: #717a8e;
                    box-shadow: 0 0 4px #000;
                    display: flex;
                    justify-content: center;
                }
                .switches {
                    display: flex;
                    justify-content: center;
                    flex-direction: column;
                }
                .switch {
                    margin: 8px;
                    display: flex;
                    flex-direction: column-reverse;
                    align-items: center;
                    position: relative;
                    height: 100px;
                }
                .switch input {
                    position: absolute;
                    opacity: 0;
                    width: 100%;
                    height: 100%;
                    cursor: pointer;
                }
                .switch input ~ label + .icon {
                    height: 100px;
                    width: 100px;
                    background-image: url("kamera-off.png");
                }
                .switch input:checked ~ label + .icon {
                    background-image: url("kamera-on.png");
                }
                .switch input ~ label b.off {
                    display: inline;
                }
                .switch input ~ label b.on {
                    display: none;
                }
                .switch input:checked ~ label b.off {
                    display: none;
                }
                .switch input:checked ~ label b.on {
                    display: inline;
                }
            </style>
        </head>
        <body>
            <header>
                <h2>Kamerasteuerung</h2>
            </header>
            <div class="switches">
                <div class="switch">
                    <input type="checkbox" class="toggle" data-pin="17">
                    <label>Kamera 1 ist <b class="state"><?php echo isPinOn(17) ? "an" : "aus"?></b></label>
                    <div class="icon"></div>
                </div>
                <div class="switch">
                    <input type="checkbox" class="toggle" data-pin="23">
                    <label>Kamera 2 ist <b class="state"><?php echo isPinOn(23) ? "an" : "aus"?></b></label>
                    <div class="icon"></div>
                </div>
            </div>
            <script>
                const elems = document.querySelectorAll('input[type="checkbox"].toggle');
                for(let i = 0; i < elems.length; i++){
                    const el = elems[i];
                    if(el && el.dataset.pin){
                        el.addEventListener('change', function() {
                            console.log(el.dataset.pin, el.checked);
                            const formData = new FormData();
                            formData.append('pin', el.dataset.pin);
                            formData.append('on', el.checked);
                            el.disabled = true;
                            fetch(location.href, {
                                method: "POST",
                                body: formData,
                            })
                            .then(function(response) {
                                if(response.status == 200){
                                    return Promise.resolve(response);
                                }else{
                                    return Promise.reject('Leider ist bei der Anfrage ein Fehler aufgetreten.');
                                }
                            })
                            .then(function(response){
                                return response.json();
                            })
                            .then(function(json){
                                console.log(json);
                                el.checked = json.on;
                            })
                            .catch(function(err) {
                                el.checked = !el.checked;
                                alert(err);
                            })
                            .finally(function() {
                                el.disabled = false;
                            });
                        })
                    }
                }
            </script>
        </body>
</html>
<?php endif; ?>