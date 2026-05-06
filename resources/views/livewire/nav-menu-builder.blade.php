<div>
    <ul id="builder_root">
        <li data-page-id="1">
            1
            <ul>
                <li data-page-id="2">
                    2
                    <ul>
                        <li data-page-id="4">4</li>
                        <li data-page-id="5">5</li>
                        <li data-page-id="69">69</li>
                        <li data-page-id="420">420</li>
                    </ul>
                </li>
                <li data-page-id="3">
                    3
                    <ul>
                        <li data-page-id="6">6</li>
                        <li data-page-id="67">67</li>
                    </ul>
                </li>
            </ul>
        </li>
        <li data-page-id="7">7</li>
    </ul>
    <div>
        <button id="save-changes-btn">Save</button>
    </div>

    <script>
        document
        .getElementById("save-changes-btn")
        .addEventListener("click", () => {
            const builder = document.getElementById("builder_root");
            const tree = builder.querySelectorAll("&>[data-page-id]")

            let resultsArray = [];
            walk(tree, null, resultsArray)
            console.log(resultsArray)
        })

        function walk(items, parentId, resultsArray) {
            let position = 0;

            items.forEach(item => {
                const page_id = item.getAttribute('data-page-id');
                const resultItem = {page_id: page_id, parent_id: parent, position};

                resultsArray.push(resultItem);
                position++;

                const children = item.querySelectorAll("&>ul>[data-page-id]")
                if(children.length === 0) {
                    return;
                }

                walk(children, page_id, resultsArray);
            })
        }
    </script>
</div>
