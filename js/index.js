$(function () {
    let support = "/cgi-bin/support.php";
    $.getJSON(support, { action: 'gettable' }).then(data => {

        let table = $("<table>").addClass("table table-dark table-striped outer");
        let thead = $("<thead>").css({
            position: "sticky",
            top: 0
        });
        let tr = $("<tr>");
        table.append(thead);
        thead.append(tr);
        let columns = ["POST", "PERMALINK", "LINKS"];
        let wcolumns = ["15%", "25%", "60%"];
        for (let ic in columns) {
            tr.append($("<td>").attr('width', wcolumns[ic]).text(columns[ic]));
        }
        columns = ["POST", "TYPE", "LINK", "PERMALINK"];
        let tbody = $("<tbody>");
        table.append(tbody);
        if (false) {
            $('#exampleModalLong button').off('click');
            $('#exampleModalLong button').on('click', event => {
                let action = $(event.currentTarget).text();
                let post = $("#exampleModalLong table td[data-type='POST']").text();
                let type = $("#exampleModalLong table td[data-type='TYPE']").text();
                let link = $("#exampleModalLong table td[data-type='LINK'] input").val();
                let tr = $('#exampleModalLong').prop('data-tr');
                let td = tr.find("td[data-type='LINK']");
                if (action == 'Close') {

                }
                else if (action == 'Delete') {
                    $.getJSON(support, {
                        action: 'delete',
                        POST: post,
                        TYPE: type
                    }).done(data => {
                        td.text(null);
                        console.log(data);
                    }).fail((data, textStatus, error) => {
                        console.log(textStatus);
                    });

                }
                else if (action == 'Save changes') {
                    $.getJSON(support, {
                        action: 'save',
                        POST: post,
                        LINK: link,
                        TYPE: type
                    }).done(data => {
                        td.text(link);
                        console.log(data);
                    }).fail((data, textStatus, error) => {
                        console.log(textStatus);
                    });
                }
                $('#exampleModalLong').modal('hide');
                return;
            });
            for (let d of data) {
                let tr = $("<tr>");
                tbody.append(tr);
                for (let idd in d) {
                    let dd = d[idd];
                    let td;
                    if (columns[idd] == 'PERMALINK') {
                        td = $("<td>").addClass("text-truncate").attr("data-type", columns[idd]).
                            append($("<a>").attr({ "href": dd, "target": "_blank" }).text(dd));
                    }
                    else {
                        td = $("<td>").addClass("text-truncate").attr("data-type", columns[idd]).text(dd);
                    }
                    tr.append(td);
                    td.on("click", function (event) {
                        if ($(event.target).prop("tagName") == 'A') {
                            return;
                        }
                        // alert("click");
                        $('#exampleModalLong').prop('data-tr', $(event.target).parent());
                        let tds = $(event.target).parent().find("td").map((i, e) => $(e).text());
                        $('#exampleModalLong .modal-body').empty();
                        let table = $("<table>").addClass("table table-striped w-auto')");
                        $('#exampleModalLong .modal-body').append(table);
                        let tbody = $("<tbody>");
                        table.append(tbody);
                        for (let it = 0; it < tds.length; ++it) {
                            let tr = $("<tr>");
                            tr.append($("<td>").text(columns[it]));
                            let td = $("<td>").attr({ 'data-type': columns[it] });
                            tr.append(td);
                            if (columns[it] == 'LINK') {
                                td.append($("<input>").attr("type", "text").attr("value", tds[it]));
                            }
                            else if (columns[it] == 'PERMALINK') {
                                td.append($("<a>").attr({ "href": tds[it], "target": "_blank" }).text(tds[it]));
                            }
                            else {
                                td.text(tds[it]);
                            }
                            tbody.append(tr);
                        }

                        $('#exampleModalLong').modal('show');
                        return;
                    });
                }
            }
        }
        else {
            $('#exampleModalLong button').off('click');
            $('#exampleModalLong button').on('click', event => {
                let target = $(event.currentTarget);
                if (target.text() == 'Clear') {
                    $("#exampleModalLong input").val('');
                    return;
                }
                else if (target.text() == 'Save changes') {
                    let rows =
                        $("#exampleModalLong tr").map((i, tr) => [
                            $(tr).find("td")
                        ]);
                    let values = {};
                    for (let row of rows) {
                        let name = $(row[0]).text();
                        if (name == 'POST') {
                            post = $(row[1]).text();
                        }
                        else if (name == 'PERMALINK') {
                            permalink = $(row[1]).text();
                        }
                        else {
                            values[name] = row[1];
                        }
                    }
                    for (let type of Object.keys(values)) {
                        let td = $(values[type]);
                        let value = td.find("input").val();
                        let link = value.length ? value : null;
                        $.getJSON(support, {
                            action: 'save',
                            POST: post,
                            LINK: link,
                            TYPE: type
                        }).done(data => {
                            $(td.prop('data-td')).text(link);
                            console.log(data);
                        }).fail((data, textStatus, error) => {
                            console.log(textStatus);
                        });

                    }

                }
                else if (target.text() == 'Close') { }
                $('#exampleModalLong').modal('hide');
                return;
            });
            let row0 = null;
            let currentid = null;
            let innertable, innertbody;
            let modal = $('#exampleModalLong');
            function handleclick(e) {
                let ee = $(e.currentTarget);
                let values = ee.parent().find("table tr ").map((i, tr) => [$(tr).find("td")]);
                let posts = ee.parent().find("td.outer").map((i, e) => $(e).text());
                let modalbody = modal.find('.modal-body');
                modalbody.empty();
                let table = $("<table>");
                let tbody = $("<tbody>");
                modalbody.append(table.append(tbody));
                let tr;
                tr = $("<tr>");
                tbody.append(tr);
                td = $("<td>").text("POST");
                tr.append(td);
                td = $("<td>").text(posts[0]);
                tr.append(td);
                //
                tr = $("<tr>");
                tbody.append(tr);
                td = $("<td>").text("PERMALINK");
                tr.append(td);
                td = $("<td>").text(posts[1]);
                tr.append(td);
                //
                for (let value of values) {
                    tr = $("<tr>");
                    tbody.append(tr);
                    td = $("<td>").text($(value[0]).text());
                    tr.append(td);
                    td = $("<td>").append($("<input>").attr("type", "text").attr("value", $(value[1]).text())).prop('data-td', value[1]);
                    tr.append(td);
                }
                $('#exampleModalLong').modal('show');
                return;
            };
            for (let d of data) {
                if (currentid != d[0]) {
                    let tr = $("<tr>");
                    tbody.append(tr);
                    currentid = d[0];
                    let td = $("<td>").addClass('outer').attr({ 'width': wcolumns[0], "data-type": columns[0] }).text(currentid);
                    tr.append(td);
                    td.on("click", handleclick);
                    td = $("<td>").addClass('outer').attr('width', wcolumns[1]).
                        addClass("text-truncate").attr("data-type", columns[1]).
                        append($("<a>").attr({ "href": d[3], "target": "_blank" }).text(d[3]));
                    tr.append(td);
                    // td.on("click", handleclick);
                    innertbody = $("<tbody>");
                    innertable = $("<table>").append(innertbody);
                    tr.append($("<td>").attr('width', wcolumns[2]).append(innertable));
                }
                tr = $("<tr>");
                innertbody.append(tr);
                for (let idd of [1, 2]) {
                    let dd = d[idd];
                    let td;
                    if (columns[idd] == 'PERMALINK') {
                        td = $("<td>").addClass("text-truncate").attr("data-type", columns[idd]).
                            append($("<a>").attr({ "href": dd, "target": "_blank" }).text(dd));
                    }
                    else {
                        td = $("<td>").addClass("text-truncate").attr("data-type", columns[idd]).text(dd);
                    }
                    tr.append(td);
                    /*
                    td.on("click", function (event) {
                        if ($(event.target).prop("tagName") == 'A') {
                            return;
                        }
                        // alert("click");
                        $('#exampleModalLong').prop('data-tr', $(event.target).parent());
                        let tds = $(event.target).parent().find("td").map((i, e) => $(e).text());
                        $('#exampleModalLong .modal-body').empty();
                        let table = $("<table>").addClass("table table-striped w-auto')");
                        $('#exampleModalLong .modal-body').append(table);
                        let tbody = $("<tbody>");
                        table.append(tbody);
                        for (let it = 0; it < tds.length; ++it) {
                            let tr = $("<tr>");
                            tr.append($("<td>").text(columns[it]));
                            let td = $("<td>").attr({ 'data-type': columns[it] });
                            tr.append(td);
                            if (columns[it] == 'LINK') {
                                td.append($("<input>").attr("type", "text").attr("value", tds[it]));
                            }
                            else if (columns[it] == 'PERMALINK') {
                                td.append($("<a>").attr({ "href": tds[it], "target": "_blank" }).text(tds[it]));
                            }
                            else {
                                td.text(tds[it]);
                            }
                            tbody.append(tr);
                        }

                        $('#exampleModalLong').modal('show');
                        return;
                    });
                    */
                }
            }

        }
        $("div.container").empty().append(table);
        return;
    }).fail((a, b, c) => {
        console.log(b);
    });
    $('#exampleModalLong').on('shown.bs.modal', function () {
        // $('#myInput').trigger('focus')
        return;
    });
    $('#exampleModalLong button').on("click", function () {
        $('#exampleModalLong').modal('hide');
    });
}
);