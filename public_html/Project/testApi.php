<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["symbol"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["function" => "GLOBAL_QUOTE", "symbol" => $_GET["symbol"], "datatype" => "json"];
    $endpoint = "https://alpha-vantage.p.rapidapi.com/query";
    $isRapidAPI = true;
    $rapidAPIHost = "alpha-vantage.p.rapidapi.com";
    $result = get($endpoint, "STOCK_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    if (isset($result["Global Quote"])) {
        $quote = $result["Global Quote"];
        $quote = array_reduce(
            array_keys($quote),
            function ($temp, $key) use ($quote) {
                $k = explode(" ", $key)[1];
                if ($k === "change") {
                    $k = "per_change";
                }
                $temp[$k] = str_replace('%', '', $quote[$key]);
                return $temp;
            }
        );
        $result = [$quote];
        $db = getDB();
        $query = "INSERT INTO `IT202-S24-Stocks` ";
        $columns = [];
        $params = [];
        //per record
        foreach ($quote as $k => $v) {
            array_push($columns, "`$k`");
            $params[":$k"] = $v;
        }
        $query .= "(" . join(",", $columns) . ")";
        $query .= "VALUES (" . join(",", array_keys($params)) . ")";
        var_export($query);
        try {
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            flash("Inserted record", "success");
        } catch (PDOException $e) {
            error_log("Something broke with the query" . var_export($e, true));
            flash("An error occurred", "danger");
        }
    }
}
?>
<div class="container-fluid">
    <h1>Stock Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Symbol</label>
            <input name="symbol" />
            <input type="submit" value="Fetch Stock" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $stock) : ?>
                <pre>
            <?php var_export($stock);
            ?>
            </pre>
                <table style="display: none">
                    <thead>
                        <?php foreach ($stock as $k => $v) : ?>
                            <td><?php se($k); ?></td>
                        <?php endforeach; ?>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($stock as $k => $v) : ?>
                                <td><?php se($v); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");
