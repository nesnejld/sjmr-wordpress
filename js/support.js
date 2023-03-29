var data;
var context = {};

class History {
    constructor() {
        this.current_ = -1;
        this.history = [];
    }
    add(s) {
        if (this.history.indexOf(s) == -1) {
            this.history.unshift(s);
            this.current_ = 0;
        }
    }
    previous() {
        if (this.history.length > 0) {
            this.current_ = (this.current_ + this.history.length - 1) % this.history.length;
        }
    }
    next() {
        if (this.history.length > 0) {
            this.current_ = (this.current_ + 1) % this.history.length;
        }
    }
    current() {
        return this.history.length > 0 ? this.history[this.current_] : '';
    }
}
function phpinfo() {
    return new Promise(function (resolve, reject) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", encodeURI(`../support.php?action=phpinfo`));
        xhr.send();
        xhr.responseType = "html";
        xhr.onload = () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                resolve(xhr.response);
            } else {
                console.log(`Error: ${xhr.status}`);
                reject(xhr);
            }
        };
    });
}
function mysql() {
    return new Promise(function (resolve, reject) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", encodeURI(`../support.php?action=mysql`));
        xhr.send();
        xhr.responseType = "text";
        xhr.onload = () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                resolve(xhr.response);
            } else {
                console.log(`Error: ${xhr.status}`);
                reject(xhr);
            }
        };
        // resolve( '<html><body>mysql</body></html>');
    });
}
function runcommand(command) {
    return new Promise(function (resolve, reject) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", encodeURI(`../support.php?action=shell&command=${command}`));
        xhr.send();
        xhr.responseType = "text";
        xhr.onload = () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                resolve(xhr.response);
            } else {
                console.log(`Error: ${xhr.status}`);
                reject(xhr);
            }
        };
    });
}
function runsqlcommand(command) {
    return runcommand(`mysql ${context.mysqlprefix} -e "${command}"`);
}
function mysqldump() {
    return runcommand(`mysqldump ${context.mysqlprefix} -r dump.sql`);
}

async function getfiles(dir, recursive) {
    return new Promise(function (resolve, reject) {
        result.innerHTML = `Getting ${dir}`;
        const xhr = new XMLHttpRequest();
        xhr.open("GET", dir ? `../getfiles.php?dir=${dir}` : '../getfiles.php');
        xhr.send();
        xhr.responseType = "json";
        xhr.onload = async () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                let data = xhr.response;
                for (let k in data) {
                    if (k.endsWith('.')) {
                        continue;
                    }
                    if (recursive && data[k]['type'] == 'directory'
                        && !k.startsWith("./.git")
                        && !k.startsWith("./cgi-bin")
                    ) {
                        let subdir = await getfiles(k);
                        data = Object.assign(data, subdir);
                        continue;
                    }
                    else {
                    }
                }
                resolve(data);
            } else {
                console.log(`Error: ${xhr.status}`);
                reject(xhr);
            }
        };
    });
}
function getfiles0(dir) {
    result.innerHTML = "Working ...";
    const xhr = new XMLHttpRequest();
    xhr.open("GET", dir ? `getfiles.php?dir=${dir}` : 'getfiles.php');
    xhr.send();
    xhr.responseType = "json";
    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const data = xhr.response;
            console.log(data);
            output = '';
            output += '<input type="button" class="toaws" value="To AWS" onclick="toaws()">';
            output += '<table class="filelist">';
            for (let k in data) {
                output += '<tr>';
                output += '<td><input type="checkbox" class="toaws"></td>';
                if (data[k]['type'] == 'file') {
                    output += `<td class="name file">${k}</td><td>${data[k]['type']}</td>`;
                    output += `<td>${data[k]['length']}</td>`;
                }
                else {
                    output += `<td class="name directory">${k}</td><td>${data[k]['type']}</td>`;
                }
                output += '</tr>';
            }
            output += '</table>';
            //result.innerHTML=`<pre>${data}</pre>`;
            result.innerHTML = output;
            let get = document.querySelectorAll('div#result td.name.directory');
            for (let e of get) {
                e.onclick = async function (event) {
                    // alert('OK');
                    let data = await getfiles(event.target.textContent);
                };
            }
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };

}
function toaws(all) {
    let checkedList;
    checkedList = document.querySelectorAll("table.filelist td.file");
    let files = [];
    for (let td of checkedList) {
        if (all || td.parentElement.querySelectorAll("input:checked").length == 1) {
            console.log(td.textContent);
            if (!td.textContent.endsWith('.tar') && !td.textContent.endsWith('.tgz') && !td.textContent.endsWith('.gz')) {
                files.push(td.textContent);
            }
        }
        // alert('Got here');
    }
    sendfile(files);
}
async function sendfile(files) {
    result.innerHTML = "Working ...";
    let init = true;
    for (let fileindex in files) {
        let filename = files[fileindex];
        try {
            let dir = filename.substr(0, filename.lastIndexOf('/'));
            //result.innerHTML += filename + '\n';
            await runcommand(`ssh -i .ssh/SJMR.pem -o StrictHostKeyChecking=no ubuntu@34.219.135.46 mkdir -p SJMR/${dir}`);
            let data = await runcommand(`scp -i .ssh/SJMR.pem -o StrictHostKeyChecking=no ${filename} ubuntu@34.219.135.46:SJMR/${filename}`);
            if (init) {
                init = false;
                result.innerHTML = '';
            }
            result.innerHTML = `${fileindex} ${data}\n${result.innerHTML}`;

        }
        catch (e) {
            result.innerHTML = `<pre class="error">${filename} ${e}</pre>` + result.innerHTML;
        }
    }
    return;
}
function rendertable(data) {
    files = data;
    output = '';
    output += '<input type="button" class="toaws" value="To AWS" onclick="toaws(false)">';
    output += '<input type="button" class="toaws" value="All to AWS" onclick="toaws(true)">';
    output += '<table class="filelist">';
    for (let k in data) {
        output += '<tr>';
        output += '<td><input type="checkbox" class="toaws"></td>';
        if (data[k]['type'] == 'file') {
            output += `<td class="name file">${k}</td><td>${data[k]['type']}</td>`;
            output += `<td>${data[k]['length']}</td>`;
        }
        else {
            output += `<td class="name directory">${k}</td><td>${data[k]['type']}</td>`;
        }
        output += '</tr>';
    }
    output += '</table>';
    result.innerHTML = output;
    let get = document.querySelectorAll('div#result td.name.directory');
    for (let e of get) {
        e.onclick = async function (event) {
            // alert('OK');
            let data = await getfiles(event.target.textContent).then(rendertable);
        };
    }
}
window.onload = function () {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", '../mysql.json');
    xhr.send();
    xhr.responseType = "json";
    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 200) {
            context.mysql = xhr.response;
            context.mysqlprefix = ` -h ${context.mysql.servername} -u ${context.mysql.username} -p${context.mysql.password} ${context.mysql.database} `;
            // resolve(xhr.response);
        } else {
            console.log(`Error: ${xhr.status}`);
            // reject(xhr);
        }
    };

    button = document.getElementById('button');
    command = document.getElementById('command');
    command.History = new History();
    command.runner = runcommand;
    sqlcommand = document.getElementById('sqlcommand');
    sqlcommand.History = new History();
    sqlcommand.runner = runsqlcommand;
    sqlcommand.History.add("show databases");
    sqlcommand.History.add("show tables");
    sqlcommand.value = sqlcommand.History.current();
    result = document.getElementById('result');
    estatus = document.getElementById('status');
    egetfiles = document.getElementById('getfiles');
    ephpinfo = document.getElementById('phpinfo');
    emysql = document.getElementById('mysql_init');
    emysqldump = document.getElementById('mysqldump');
    if (button) {
        button.onclick = async function (e) {
            //alert(command.value);
            data = await runcommand(command.value);
        };
    }
    let commandhandler = async function (e) {
        if (e.key == "Enter") {
            result.innerHTML = "Working ...";
            e.target.History.add(e.target.value);
            let data = await e.target.runner(e.target.value);
            result.innerHTML = data;
        }
        if (e.key == "ArrowUp") {
            e.target.History.previous();
            e.target.value = e.target.History.current();
        }
        if (e.key == "ArrowDown") {
            e.target.History.next();
            e.target.value = e.target.History.current();
        }

    };
    command.onkeydown = commandhandler;
    sqlcommand.onkeydown = commandhandler;
    egetfiles.onkeydown = function (e) {
        if (e.keyCode == 13) {
            getfiles(egetfiles.value).then(rendertable);
        }

    };
    getfiles().then(rendertable);
    ephpinfo.onclick = function () {
        phpinfo().then(data => {
            result.innerHTML = data;
        });
    };
    emysql.onclick = function () {
        mysql().then(data => {
            result.innerHTML = data;
        });
    };
    emysqldump.onclick = function () {
        mysqldump().then(data => {
            result.innerHTML = data;
        });
    };

};
document.addEventListener('readystatechange', event => {
    switch (document.readyState) {
        case "loading":
            console.log("document.readyState: ", document.readyState,
                `- The document is still loading.`
            );
            break;
        case "interactive":
            console.log("document.readyState: ", document.readyState,
                `- The document has finished loading DOM. `,
                `- "DOMContentLoaded" event`
            );
            break;
        case "complete":
            console.log("document.readyState: ", document.readyState,
                `- The page DOM with Sub-resources are now fully loaded. `,
                `- "load" event`
            );
            break;
    }
});
