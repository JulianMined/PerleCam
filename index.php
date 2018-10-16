<?php 
if($_POST['pin'] && $_POST['on']):

    function setPinOut($pin, $val){
        $val = boolval($val);
        $pin = intval($pin);
        shell_exec("/usr/local/bin/gpio -g write $pin $val");
    };

    echo setPinOut($_POST['pin'], $_POST['on']);

else: ?>
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>Kamerasteuerung</title>
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
                    background: #202838;
                    font-family: 'nokia';
                }
                header {
                    background: #2c374f;
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
                }
                .switch input:disabled ~ label + .icon, .switch input:disabled ~ label {
                    opacity: .5;
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
            <?php
                shell_exec("/usr/local/bin/gpio -g mode 17 out");
                shell_exec("/usr/local/bin/gpio -g mode 23 out");

                function isPinOn($pin){
                    return shell_exec("/usr/local/bin/gpio read $pin");
                };

            ?>
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
                document.querySelectorAll('input[type="checkbox"].toggle').forEach((el)=>{
                    if(el.dataset.pin){
                        el.addEventListener('change', ()=>{
                            console.log(el.dataset.pin, el.checked);
                            const formData = new FormData();
                            formData.append('pin', el.dataset.pin);
                            formData.append('on', el.checked);
                            el.disabled = true;
                            fetch('/', {
                                method: "POST",
                                body: formData,
                            }).then(data => {
                                console.log(data);
                                el.checked = el.checked;
                                el.parentNode.querySelector('label b.state').textContent = el.checked ? 'an' : 'aus';
                            }).catch(err => {
                                el.checked = !el.checked;
                            }).finally(()=>{
                                el.disabled = false;
                            })
                        })
                    }
                })
            </script>
        </body>
</html>
<?php endif; ?>