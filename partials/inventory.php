<script>
    function get_inventory() {
        postData({}, "/Project/api/get_inventory.php").then(data => {
            let inv = document.getElementById("inventory");
            inv.innerHTML = "";
            console.log("get_inventory", data);
            if (data.status === 200) {
                for (let item of data.items) {
                    let cell = document.createElement("div");
                    cell.className = "col";
                    cell.innerHTML =
                        `
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">${item.name}</div>
                            <div class="card-body">${item.description}</div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary" onclick="use_item(${item.item_id})">Use 1/${item.quantity}</button>
                        </div>
                    </div>
                    `;
                    inv.appendChild(cell);
                }
            } else {
                flash(data.message, "warning");
            }
        });
    }

    function use_item(item_id) {
        postData({
            item_id: item_id
        }, "/Project/api/activate_item.php").then(data => {
            console.log("use item", data);
            if (!data.status !== 200) {
                flash(data.message, "warning");
            }
            get_inventory();
        })
    }
    get_inventory();
</script>
<div class="container-fluid">
    <div id="inventory" class="row row-cols-1">

    </div>
</div>