<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}

?>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Welcome to Ler!</h1>
        <h5>Where you can come <code>to read</code> interactive stories!</h5>
        <p class="lead">
            This project lets writers create interactive stories. <br>
            The stories will contain Arcs (or sections of plot), then each Arc will give the reader 1-4 decisions.<br>
            Based on the decision selected it'll go to a different Arc (or line of plot).<br>
            The reader can continue until they reach 1 of the many endings.<br>
        </p>
    </div>
</div>