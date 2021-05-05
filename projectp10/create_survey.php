<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have access to visit this page");
    die(header("Location: login.php"));
}
?>
<div class="container">
<div class="h3">Create Survey</div>
<form method="POST" onsubmit="return save();">
    <div class="row">
        <label for="title" class="form-label">Title</label>
        <input type="text" id="title" class="s-title" required class="form-control" />
    </div>
    <div id="questions">
        <div class="row question p-1">
            <label for="q" class="form-label">Question <button class="btn btn-sm btn-danger" onclick="event.preventDefault(); removeEle(this)">x</button></label>
            
            <input type="text" class="s-question" class="form-control" required />
            <div class="answers">
                <div class="row answer p-1">
                    <label for="a" class="form-label">Answer <button class="btn btn-sm btn-danger" onclick="event.preventDefault(); removeEle(this)">x</button></label>
                    <input type="text" class="s-answer" class="form-control" required />
                    
                </div>
            </div>
            <div class="d-grid gap-2">
            <button class="btn btn-sm btn-primary" onclick="event.preventDefault(); addAnswer(this)">Add Answer</button>
            </div>
        </div>
    </div>
    <div class="d-grid gap-2">
        <button class="btn btn-sm btn-primary"  onclick="event.preventDefault(); addQuestion(this)">Add Question</button>
    </div>
    <input type="submit" class="btn btn-success mt-2" value="Create" />
</form>
</div>
<script>
    function removeEle(ele){
        let $p = $(ele).parent().parent();
        if($p.parent().children().length > 1){
            $p.remove();
        }
    }
    function updateLabels(){
        let $questions = $("#questions").first();
        console.log("questions", $questions);
        $questions.children().each( (index, q)=>{
            console.log(index, q);
            let $q = $(q);
            $q.find("label").attr("for", "q_"+index);
            $q.find(".s-question").attr("id", "q_"+index);
            let $answers = $q.find(".answers");
            $answers.children().each((ai, a)=>{
                let $a = $(a);
                $a.find("label").attr("for", "q_"+index+"_a_"+ai);
                $a.find(".s-answer").attr("id", "q_"+index+"_a_"+ai);
            });
        });
    }
    function addAnswer(ele) {
        let $answers = $(ele).parent().prev();
        let $a = $answers.children().first().clone();
        $a.val("");//clear input
        $answers.append($a);
        updateLabels();
    }

    function addQuestion(ele) {
        let $questions = $(ele).parent().prev();
        let $q = $questions.children().first().clone();
        $q.find("input").val(""); //clear inputs

        $questions.append($q);
        updateLabels();
    }

    function save() {
        let survey = {questions:[]};
        let $questions = $("#questions").first();
        survey.title = $("#title").val();
        console.log("questions", $questions);
        $questions.children().each( (index, q)=>{
            console.log(index, q);
            let $q = $(q);
            let question = {
                question: $q.find(".s-question").val(),
                answers:[]
            }
            let $answers = $q.find(".answers");
            $answers.children().each((ai, a)=>{
                let $a = $(a);
                question.answers.push({
                    answer: $a.find(".s-answer").val()
                })
            });
            survey.questions.push(question);
        });
        //TODO require questions to have at least 2 answers; reject otherwise
        console.log("survey", survey);
        $.post("api/create_survey.php", {"survey":survey}, (data, status)=>{
            console.log("Response", data, status);
        });
        return false;
    }
</script>