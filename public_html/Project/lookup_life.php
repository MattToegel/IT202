<?php
require(__DIR__ . "/../../partials/nav.php");


//build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Broker Name", "label" => "Broker Name", "include_margin" => false],
];
error_log("Form data: " . var_export($form, true));

?>
<script>
    let timeout = null;

    function liveFetch(ele) {
        //debounce logic
        if (timeout) {
            clearTimeout(timeout);
            timeout = null;
        }
        timeout = setTimeout(() => {
            fetch(`/Project/api/live_search.php?query=${ele.value}`).then(resp => resp.json())
                .then(json => {
                    let target = document.getElementsByClassName("row")[1];
                    target.innerHTML = `<div class='col-12'><pre>${JSON.stringify(json)}</pre></div>`;
                })
        }, 500)
    }
</script>
<div class="container-fluid">
    <h3>Brokers</h3>
    <form method="GET" onsubmit="return false">
        <div class="row mb-3" style="align-items: flex-end;">

            <input type="text" name="name" oninput="liveFetch(this)" />

        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>

    <div class="row w-100  g-4">

    </div>
</div>


<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>