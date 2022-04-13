<!-- this will be moved into a partial file for reusability-->
<?php
if (!isset($total)) {
    flash("Note to Dev: The total variable is undefined", "danger");
}
if (!isset($per_page)) {
    flash("Note to Dev: The per_page variable is undefined", "danger");
}
$total_pages = ceil($total / $per_page);
//updates or inserts page into query string while persisting anything already present
function persistQueryString($page)
{
    //set the query param for easily building
    $_GET["page"] = $page;
    //https://www.php.net/manual/en/function.http-build-query.php
    return http_build_query($_GET);
}
function check_apply_disabled_prev($page)
{
    echo $page < 1 ? "disabled" : "";
}
function check_apply_active($page, $i)
{
    echo ($page - 1) == $i ? "active" : "";
}
function check_apply_disabled_next($page)
{
    global $total_pages;
    echo ($page) >= $total_pages ? "disabled" : "";
}
?>

<nav aria-label="Page navigation example I hope I get changed">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php check_apply_disabled_prev(($page - 1)) ?>">
            <a class="page-link" href="?<?php se(persistQueryString($page - 1)); ?>" tabindex="-1">Previous</a>
        </li>
        <?php for ($i = 0; $i < $total_pages; $i++) : ?>
            <li class="page-item <?php check_apply_active($page, $i); ?>"><a class="page-link" href="?<?php se(persistQueryString($i + 1)); ?>"><?php echo ($i + 1); ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php check_apply_disabled_next($page); ?>">
            <a class="page-link" href="?<?php se(persistQueryString($page + 1)); ?>">Next</a>
        </li>
    </ul>
</nav>