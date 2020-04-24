$(function() {
    $("div[data-toggle=fieldset]").each(function() {
        let $this = $(this);
        //NOTE:
        //Add new entry
        $this.find("button[data-toggle=fieldset-add-row]").click(function() {
            let target = $($(this).data("target"));
            //Matt added to limit the max elements
            let limit = $(this).data("limit") || -1;
            if(limit > -1){
                let count = target.find("[data-toggle=fieldset-entry]").length;
                if(count >= limit){
                    return;
                }
            }
            console.log(target);
            let oldrow = target.find("[data-toggle=fieldset-entry]:last");
            let row = oldrow.clone(true, true);
            console.log(row.find(":input")[0]);
            let elem_id = row.find(":input")[0].id;
            let elem_num = parseInt(elem_id.replace(/.*-(\d{1,4})-.*/m, '$1')) + 1;
            row.attr('data-id', elem_num);
            row.find(":button").each(function() {
                console.log(this);
                let id = $(this).attr('id').replace('-' + (elem_num - 1) + '-', '-' + (elem_num) + '-');
                $(this).attr('name', id).attr('id', id).val('').removeAttr("checked");
            });
            oldrow.after(row);
        }); //End add new entry

        //Remove row
        $this.find("button[data-toggle=fieldset-remove-row]").click(function() {
            if($this.find("[data-toggle=fieldset-entry]").length > 1) {
                let thisRow = $(this).closest("[data-toggle=fieldset-entry]");
                thisRow.remove();
            }
        }); //End remove row
    });
});