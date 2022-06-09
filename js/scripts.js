// ajax-скрипт для отправки данных
function ajaxScript() {
        if (check() === false) {
                return;
        }
        let res = true;
        let form = new FormData(document.getElementById('mail_form'));
        fetch('/intaro-PHP/mailer/mailer.php', {
                    method: 'POST',
                    body: form
            }
        )
            .then(response => response.json())
            .then((result) => {
                    document.getElementById('mail_form').innerText = "";
                    document.getElementById('list').innerText = "";

                    if (result.timeError === false) {
                            let a = document.createElement("a");
                            a.innerText = "Имя: " + result.firstname;
                            a.className = "list-group-item list-group-item-action";
                            document.getElementById('list').appendChild(a);

                            a = document.createElement("a");
                            a.innerText = "Фамилия: " + result.secondname;
                            a.className = "list-group-item list-group-item-action";
                            document.getElementById('list').appendChild(a);

                            a = document.createElement("a");
                            a.innerText = "Отчество: " + result.patronymic;
                            a.className = "list-group-item list-group-item-action";
                            document.getElementById('list').appendChild(a);

                            a = document.createElement("a");
                            a.innerText = "Электронная почта: " + result.email;
                            a.className = "list-group-item list-group-item-action";
                            document.getElementById('list').appendChild(a);

                            a = document.createElement("a");
                            a.innerText = "Телефон: " + result.phone;
                            a.className = "list-group-item list-group-item-action";
                            document.getElementById('list').appendChild(a);

                            var currentdate = new Date();
                            var formTime = new Date(currentdate.getTime() + 90 * 60000);
                            var datetime = formTime.getHours() + ":"
                                + formTime.getMinutes() + ":"
                                + formTime.getSeconds() + " "
                                + formTime.getDate() + "."
                                + (formTime.getMonth() + 1) + "."
                                + formTime.getFullYear();

                            a = document.createElement("a");
                            a.innerText = "С вами свяжутся после " + datetime + ".";
                            a.className = "list-group-item list-group-item-action";
                            document.getElementById('list').appendChild(a);
                    } else {
                            a = document.createElement("a");
                            a.innerText = "С момента вашей последней заявки прошло менее часа! Отправить новую заявку можно будет в " + result.time;
                            a.className = "list-group-item list-group-item-action";
                            document.getElementById('list').appendChild(a);
                    }
            })
            .catch(error => console.log(error));
}
// проверка правильности введенных данных
function check() {
        let email = document.getElementById('email').value;
        let FIO = document.getElementById('FIO').value;
        let phone = document.getElementById('phone').value;

        let flag = true;

        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
                document.getElementById('email').style.borderColor = 'green';
        } else {
                document.getElementById('email').style.borderColor = 'red';
                flag = false;
        }
        if (/^8[0-9]{10}$/.test(phone)) {
                document.getElementById('phone').style.borderColor = 'green';
        } else {
                document.getElementById('phone').style.borderColor = 'red';
                flag = false;

        }
        if (/^[А-Яа-я]+\s[А-Яа-я]+\s[А-Яа-я]+$/.test(FIO)) {
                document.getElementById('FIO').style.borderColor = 'green';

        } else {
                document.getElementById('FIO').style.borderColor = 'red';
                flag = false;

        }

        return flag;
}

