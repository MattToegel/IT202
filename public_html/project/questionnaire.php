<?php
include_once(__DIR__."/partials/header.partial.php");

if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    if(!Common::has_role("Admin")){
        die(header("Location: home.php"));
    }
}
$last_updated = Common::get($_SESSION, "last_sync", false);
?>
<div class="container-fluid">
<form method="POST">
    <div class="form-group">
        <label for="questionnaire_name">Questionnaire Name</label>
        <input class="form-control" type="text" id="questionnaire_name" name="questionnaire_name"/>
    </div>
    <div class="form-group">
        <label for="questionnaire_desc">Questionnaire Description</label>
        <textarea class="form-control" type="text" id="questionnaire_desc" name="questionnaire_desc"></textarea>
    </div>
    <div class="form-group">
        <label for="attempts_per_day">Attempts per day</label>
        <input class="form-control" type="number" id="attempts_per_day" name="attempts_per_day"/>
    </div>
    <div class="form-group">
        <label for="max_attempts">Max Attempts</label>
        <input class="form-control" type="number" id="max_attempts" name="max_attempts"/>
    </div>
    <div class="form-group">
        <label for="use_max">Use Max?</label>
        <input class="form-control" type="checkbox" id="use_max" name="use_max"/>
    </div>
    <div class="list-group">
        <div class="list-group-item">
            <div class="form-group">
                <label for="question_0">Question</label>
                <input class="form-control" type="text" id="question_0" name="question_0"/>
            </div>
            <div class="list-group">
                <div class="list-group-item">
                    <div class="form-group">
                        <label for="question_0_answer_0">Answer</label>
                        <input class="form-control" type="text" id="question_0_answer_0" name="question_0_answer_0"/>
                    </div>
                    <div class="form-group">
                        <label for="question_0_answer_oe_0">Allow Open Ended?</label>
                        <input class="form-control" type="checkbox" id="question_0_answer_oe_0" name="question_0_answer_oe_0"/>
                    </div>
                </div>
            </div>
            <button class="btn btn-secondary" onclick="event.preventDefault(); cloneThis(this);">Add Answer</button>
        </div>
    </div>
    <button class="btn btn-secondary" onclick="event.preventDefault(); cloneThis(this);">Add Question</button>
    <div class="form-group">
        <input type="submit" name="submit" class="btn btn-primary" value="Create Questionnaire"/>
    </div>
</form>
<?php
    if(Common::get($_POST, "submit", false)){
        echo "<pre" . var_export($_POST, true) . "</pre>";
    }
?>
<script>
    function update_names_and_ids($ele){
        let $lis = $ele.children(".list-group-item");
        //loop over all list-group-items of list-group
        $lis.each(function(index, item){
           let $fg = $(item).find(".form-group");
           let liIndex = index;
           //loop over all form-groups inside list-group-item
           $fg.each(function(index, item){
               let $label = $(item).find("label");
               if(typeof($label) !== 'undefined' && $label != null){
                   let forAttr = $label.attr("for");
                   let pieces = forAttr.split('_');
                   //Note this is different since it's a plain array not a jquery object
                   pieces.forEach(function(item, index){
                       if(!isNaN(item)){
                           pieces[index] = liIndex;
                       }
                   });
                   let updatedRef = pieces.join("_");
                   $label.attr("for", updatedRef);
                   let $input = $(item).find(":input");
                   if(typeof($input) !== 'undefined' && $input != null){
                       $input.attr("id", updatedRef);
                       $input.attr("name", updatedRef);
                   }
               }
           });
           //See if we have any children list-groups (this would be our answers)
           let $child_lg = $(item).find(".list-group");//probably doesn't need an each loop but it's fine
           $child_lg.each(function(index, item){
               let $childlis = $(item).find(".list-group-item");
               $childlis.each(function (index, item) {
                   let $fg = $(item).find(".form-group");
                   let childLiIndex = index;
                   //loop over all form-groups inside list-group-item
                   $fg.each(function(index, item){
                       let $label = $(item).find("label");
                       if(typeof($label) !== 'undefined' && $label != null){
                           let forAttr = $label.attr("for");
                           let pieces = forAttr.split('_');
                           //Note this is different since it's a plain array not a jquery object
                           let lastIndex = -1;
                           pieces.forEach(function(item, index){
                               if(!isNaN(item)){
                                   if(lastIndex == -1) {
                                       pieces[index] = liIndex;//replace the first # with the parent outer loop index
                                       lastIndex = index;
                                   }
                                   else{
                                       pieces[index] = childLiIndex;//replace the second # with the child loop index
                                   }
                               }
                           });
                           let updatedRef = pieces.join("_");
                           $label.attr("for", updatedRef);
                           let $input = $(item).find(":input");
                           if(typeof($input) !== 'undefined' && $input != null){
                               $input.attr("id", updatedRef);
                               $input.attr("name", updatedRef);
                           }
                       }
                   });
               });
           });
        });
    }
    function cloneThis(ele){
        let $lg = $(ele).siblings(".list-group");
        let $li = $lg.find(".list-group-item:first");
        let $clone = $li.clone();
        $lg.append($clone);
        update_names_and_ids($(".list-group:first"));
    }
</script>
</div>