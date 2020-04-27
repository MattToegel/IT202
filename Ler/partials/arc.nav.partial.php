<?php
if(!isset($arcs)){
    $arcs = array();
}
?>
<?php if(count($arcs) > 0):?>
    <div class="alert alert-secondary mt-3">Quick Navigate to Arc</div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <ul class="navbar-nav">
            <?php foreach($arcs as $_arc):?>
                <li class="nav-item">
                    <a href="index.php?arc/edit&arc=<?php Utils::show($_arc,"id");?>"
                       class="nav-link">
                        <?php Utils::show($_arc,"title");?>
                    </a>
                </li>
            <?php endforeach;?>
        </ul>
    </nav>
<?php endif;?>