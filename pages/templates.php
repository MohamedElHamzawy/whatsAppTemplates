<?php
global $wpdb;
if (isset($_GET["template"]) && !empty($_GET["template"])) {
    $id = $_GET["template"];
    $template = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "whatsapp_templates WHERE id = $id");
?>
    <h1 class="headline"><?php echo $template->template_name; ?> Template</h1>
    <input type="hidden" id="tempId" value="<?php echo $id; ?>">

    <div class="addTeam">
        <label for="team">Add New Team To This Template</label>
        <select name="team" id="team">
            <option value="" selected disabled>Select Team</option>
            <?php
            $teams = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "whatsapp_teams");
            foreach ($teams as $team) {
                echo "<option value='" . $team->id . "'>" . $team->team_name . "</option>";
            }
            ?>
        </select>

        <div style="display: flex; justify-content: start;" id="teams"></div>
        <button type="button" class="btnCreate" id="addTeam">Add</button>
    </div>

    <div class="details">
        <div class="editTemplate">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo $template->template_name; ?>" readonly>
            <label for="status">Status</label>
            <select name="status" id="status" disabled>
                <option value="" selected disabled>Select Status</option>
                <option value="active" <?php if ($template->template_status == "active") {
                                            echo "selected";
                                        } ?>>Active</option>
                <option value="inactive" <?php if ($template->template_status == "inactive") {
                                                echo "selected";
                                            } ?>>Inactive</option>
            </select>
            <label for="content">Content</label>
            <input type="text" id="content" name="content" value="<?php echo $template->content; ?>" readonly>
        </div>
        <div class="editTemplate">
            <label for="teams">Teams</label>
            <ol>
                <?php
                foreach (explode(",", $template->teams_id) as $teamId) {
                    $getTeamName = $wpdb->get_var("SELECT team_name FROM " . $wpdb->prefix . "whatsapp_teams WHERE id = $teamId");
                    echo "<li>$getTeamName</li>";
                }
                ?>
            </ol>
        </div>
    </div>
<?php
} else {
?>
    <h1 class="headline">Templates</h1>
    <div class="newTemplate">
        <label for="name">Name</label>
        <input type="text" id="name" name="name">
        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="" selected disabled>Select Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <label for="content">Content</label>
        <input type="text" id="content" name="content"></input>
        <button type="button" class="btnCreate" id="addTemplate">Add Template</button>
    </div>
    <div id="templates">
        <table class="productTable">
            <thead>
                <tr class="table-sub-title">
                    <th>Name</th>
                    <th>Status</th>
                    <th>Content</th>
                    <th>Teams</th>
                </tr>
            </thead>
            <tbody id="templates-table-body">
            </tbody>
        </table>
    </div>
<?php
}
?>
<script>
    const websiteUrl = '<?php echo get_site_url(); ?>';  // Assuming script is within WordPress
    jQuery(document).ready(function() {
        // Get all templates API
        let tbody = jQuery("#templates-table-body");
        if (tbody) {
            jQuery.get(
                `${websiteUrl}/wp-json/whatsapp/v1/templates`,
                function(data) {
                    for (let i = 0; i < data.length; i++) {
                        tbody.append(`
                        <tr>
                            <td><a href="${websiteUrl}/wp-admin/admin.php?page=whatsapp-templates&template=${data[i].id}">${data[i].template_name}</a></td>
                            <td>${data[i].template_status}</td>
                            <td>${data[i].content}</td>
                            <td>${data[i].teams_id}</td>
                        </tr>
                    `);
                    }
                }
            )
        }
    });

    let addTemplateBtn = document.getElementById("addTemplate");
    if (addTemplateBtn) {
        addTemplateBtn.addEventListener("click", function() {
            let name = document.getElementById("name").value;
            let status = document.getElementById("status").value;
            let content = document.getElementById("content").value;
            let data = {
                name: name,
                status: status,
                content: content
            };
            jQuery.post(
                `${websiteUrl}/wp-json/whatsapp/v1/templates`,
                data,
                function(data) {
                    alert(data.message);
                    window.location.reload();
                },///////////////////*********************************************************************************************************************-++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++------------------------------+ */
            );
        })
    }

    let teamsArray = [];
    let teamsIdArray = [];
    let teamSelect = document.getElementById("team");
    if (teamSelect) {
        let teamsDiv = document.getElementById("teams");

        function deleteTeam(id, id2) {
            teamsArray.splice(teamsArray.indexOf(id), 1);
            teamsIdArray.splice(teamsIdArray.indexOf(id2), 1);
            teamsDiv.innerHTML = "";
            for (let i = 0; i < teamsArray.length; i++) {
                teamsDiv.innerHTML += `<div onclick="deleteTeam('${teamsArray[i]}', '${teamsIdArray[i]}')" class="team">${teamsArray[i]}</div>`;
            }
        }
        teamSelect.addEventListener("change", function(e) {
            let teamId = e.target.selectedOptions[0].value;
            let teamValue = e.target.selectedOptions[0].text;

            if (!teamsArray.includes(teamValue)) {
                teamsArray.push(teamValue);
                teamsIdArray.push(teamId);
            }

            teamsDiv.innerHTML = "";
            for (let i = 0; i < teamsArray.length; i++) {
                teamsDiv.innerHTML += `<div onclick="deleteTeam('${teamsArray[i]}', '${teamsIdArray[i]}')" class="team">${teamsArray[i]}</div>`;
            }
        })
    }

    let addTeamBtn = document.getElementById("addTeam");
    if (addTeamBtn) {
        addTeamBtn.addEventListener("click", function() {
            let tempId = document.getElementById("tempId").value;
            jQuery.post(
                `${websiteUrl}wp-json/whatsapp/v1/templates/${tempId}`, {
                    teams: teamsIdArray
                },
                function(data) {
                    alert(data.message);
                    window.location.reload();
                }
            )
        })
    }
</script>