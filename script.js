let switches = [{
        pin: 17,
        on: false,
        name: 'Kamera 1',
    },
    {
        pin: 23,
        on: false,
        name: 'Kamera 2',
    }
];

function checkState(currentSwitch) {
    console.log('checkState', currentSwitch);
    const formData = new FormData();
    formData.append('pin', currentSwitch.pin);

    return fetch(location.href, {
            method: "POST",
            body: formData,
        })
        .then(function (response) {
            return response.json();
        });
}

function changeSwitch(switchElement, currentSwitch) {
    const formData = new FormData();
    formData.append('pin', currentSwitch.pin);
    formData.append('on', switchElement.checked);

    switchElement.disabled = true;

    return fetch(location.href, {
            method: "POST",
            body: formData,
        })
        .then(function (response) {
            if (response.status == 200) {
                return Promise.resolve(response);
            } else {
                return Promise.reject('Leider ist bei der Anfrage ein Fehler aufgetreten.');
            }
        })
        .then(function (response) {
            return response.json();
        });
}

function setSwitchLabel(switchLabelElement, currentSwitch) {
    switchLabelElement.textContent = `${currentSwitch.name} ist ${currentSwitch.on ? 'an' : 'aus'}`;
}

const switchesElement = document.getElementById('switches');
for (let i = 0; i < switches.length; i++) {
    const currentSwitch = switches[i];
    const switchWrapperElement = document.createElement('div');
    switchWrapperElement.classList.add('switch');
    const switchElement = document.createElement('input');
    switchElement.type = 'checkbox';
    switchElement.classList.add('toggle');
    const switchLabelElement = document.createElement('label');
    setSwitchLabel(switchLabelElement, currentSwitch);
    const switchIconElement = document.createElement('div');
    switchIconElement.classList.add('icon');

    switchWrapperElement.append(switchElement);
    switchWrapperElement.append(switchLabelElement);
    switchWrapperElement.append(switchIconElement);
    switchesElement.append(switchWrapperElement);

    checkState(currentSwitch)
        .then(function (json) {
            switchElement.checked = json.on;
            currentSwitch.on = json.on;

            setSwitchLabel(switchLabelElement, currentSwitch);
        });

    switchElement.addEventListener('change', function () {
        changeSwitch(switchElement, currentSwitch)
            .then(function (json) {
                console.log(json);
                switchElement.checked = json.on;
                currentSwitch.on = json.on;
                setSwitchLabel(switchLabelElement, currentSwitch);
            })
            .catch(function (err) {
                switchElement.checked = !switchElement.checked;
                alert(err);
            })
            .finally(function () {
                switchElement.disabled = false;
            });
    });
}