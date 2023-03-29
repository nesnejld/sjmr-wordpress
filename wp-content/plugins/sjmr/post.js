var sjmrcontext = {};
jQuery(() => {
    let $ = jQuery;
    sjmrcontext.post = (id) => {
        // alert(`post.js: ${id}`);
        let $ = jQuery;
        let support = "/cgi-bin/support.php";
        if (!sjmrcontext.modal) {
            let html = `
        <div class="modal fade" id="mediamodal" tabindex="-1" role="dialog" aria-labelledby="mediamodalTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="mediamodalTitle">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger clear" data-dismiss="modal">Clear</button>
          <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary save close">Save changes</button>
        </div>
      </div>
    </div>
  </div>`;
            let css = `<style>
  .modal-body tbody {
      display: block;
      overflow: auto;
  }
  
  .modal-content {
      width: 700px;
  
  }
  
  .modal-content input {
      width: 100%;
  
  }
  thead,
tbody tr {
display: table;
width: 100%;
table-layout: fixed;
}
  </style>`;
            // html = `<div style='width:500px,height:500px; background:red'>Content</div>`;
            sjmrcontext.modal = $(html);

            sjmrcontext.modal.find('button.close').on("click", (event) => {
                sjmrcontext.modal.css({ 'display': 'none' });
                if ($(event.currentTarget).hasClass('save')) {
                    let values = $(event.currentTarget).prop('data-value');
                    for (let value of values) {
                        let type = value[1];
                        let tr = sjmrcontext.modal.find(`tr[data-type=${type}]`);
                        let link = tr.find("input").val();
                        value[2] = link;
                        let post = value[0];
                        $.getJSON(support, {
                            action: 'save',
                            TYPE: type,
                            POST: post,
                            LINK: link
                        }).done(data => {
                            console.log(data);
                        }).fail((data, textStatus, error) => {
                            console.log(textStatus);
                        });
                    }

                }
            });
            sjmrcontext.modal.find('button.clear').on("click", (event) => {
                let values = $(event.currentTarget).prop('data-value');
                for (let value of values) {
                    let type = value[1];
                    let tr = sjmrcontext.modal.find(`tr[data-type=${type}]`);
                    let link = null;
                    value[2] = link;
                }
                buildtable(values);
            });
            $("body").prepend(sjmrcontext.modal);
            $('head').append($(css));
        }
        let buildtable = (values) => {
            let modalbody = sjmrcontext.modal.find('.modal-body');
            modalbody.empty();
            let table = $("<table>");
            let tbody = $("<tbody>");
            modalbody.append(table.append(tbody));
            let tr;
            if (false) {
                tr = $("<tr>");
                tbody.append(tr);
                td = $("<td>").text("POST");
                tr.append(td);
                // td = $("<td>").text(posts[0]);
                tr.append(td);
                //
                tr = $("<tr>");
                tbody.append(tr);
                td = $("<td>").text("PERMALINK");
                tr.append(td);
                // td = $("<td>").text(posts[1]);
                tr.append(td);
                //
            }
            let links = {
                'facebook': "https://www.facebook.com/sanjuanmountainrunners",
                "instagram": "https://www.instagram.com/sanjuanmountainrunners/",
                "map": "https://www.google.com/maps/"
            };
            for (let value of values) {
                tr = $("<tr>").attr('data-type', value[1]);
                tbody.append(tr);
                td = $("<td>").text(value[1]);
                tr.append(td);
                td = $("<td>");
                tr.append(td);
                let innertable = $("<table>");
                let innertbody = $("<tbody>");
                td.append(innertable);
                innertable.append(innertbody);
                tr = $("<tr>");
                innertbody.append(tr);
                td = $("<td>").append($("<input>").attr("type", "text").attr("value", value[2]));
                tr.append(td);
                tr = $("<tr>");
                innertbody.append(tr);
                td = $("<td>").append($("<a>").attr("href", links[value[1]]).text(links[value[1]]));
                tr.append(td);
            }
        };
        $.getJSON(support, { action: 'getpost', POST: id }).then(values => {
            sjmrcontext.modal.find('.modal-title').text(`${values[0][0]} ${values[0][3]}`);
            buildtable(values);
            sjmrcontext.modal.find('button').prop('data-value', values);
            sjmrcontext.modal.css({
                // background: 'yellow',
                'display': 'block',
                opacity: 1,
                position: 'fixed',
                top: '200px',
                left: '100px',
                // width:'500px',
                // height:'500px', 
                'z-index': 10000
            });
            $(sjmrcontext.modal).find('.modal-content').css({
                'margin-top': '50px',
                border: 'solid blue 5px'
            });
            return;
        });
    };
});