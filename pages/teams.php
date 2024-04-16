<?php
global $wpdb;
if (isset($_GET["team"]) && !empty($_GET["team"])) { 
    $id = $_GET["team"];
    $team = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "whatsapp_teams WHERE id = $id");
    ?>

<h1 class="headline"<?php echo $team->team_name; ?>>Teams</h1>
<input type="hidden" id="teamId" value="<?php echo $id; ?>">


<div class="addMember">
        <label for="ember">Add New member To This Template</label>
        <select name="team" id="member">
            <option value="" selected disabled>Select Members</option>
            <?php
            $members = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "team_members");
            foreach ($members as $member) {
                echo "<option value='" . $member->id . "'>" . $member->name . "</option>";
            }
            ?>
        </select>

        <div style="display: flex; justify-content: start;" id="members"></div>
        <button type="button" class="btnCreate" id="addMember">Add</button>
    </div>

    <div class="details">
        <div class="editTeam">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo $team->team_name; ?>" readonly>
         </div>
        <div class="editTeam">
            <label for="members">Members</label>
            <ol>
                <?php
                if (isset($team->team_members) ){
                foreach (explode(",", $team->team_members) as $member_id) {
                    
                    $prepared_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}team_members WHERE id = %d", $member_id);
                    $getMemberId = $wpdb->get_results($prepared_query);
                    // var_dump($getMemberId);
                    echo "<li id='member-list-id-",$getMemberId[0]->id,"'>",$getMemberId[0]->name,"</li>";
                }}
                ?>
            </ol>
        </div>
    </div>

<?php 
}
else {
?>

<h1 class="headline" >Teams</h1>

<div class="newTeam">
<label>Name</label>
<input type="text" id='team-name-input'>
<button id='add-team-btn' class="btnCreate"  >Add Team</button>
</div>

<table class="productTable" >
            <thead>
                <tr class="table-sub-title" >
                    <th>Team Name</th>
                    <th>Team Members</th>
                    <th>Team ID</th>
                    
                </tr>
            </thead>
            <tbody id="teams-table-body">
            </tbody>
        </table>
<?php } ?>
    <script>
    const websiteUrl = '<?php echo get_site_url(); ?>';  // Assuming script is within WordPress
    jQuery(document).ready(function() {
        // Get all templates API
        let tbody = jQuery("#teams-table-body");
        if (tbody) {
            jQuery.get(
                `${websiteUrl}/wp-json/whatsapp/v1/teams`,
                function(data) {
                    for (let i = 0; i < data.length; i++) {
                        tbody.append(`
                        <tr>
                            <td><a href="${websiteUrl}/wp-admin/admin.php?page=whatsapp-teams&team=${data[i].id}">${data[i].team_name}</a></td>
                            <td>${data[i].team_members}</td>
                            <td>${data[i].id}</td>
                        </tr>
                    `);
                    }
                }
            )
        }
    });

    let addTeamBtn = document.getElementById("add-team-btn");
    if (addTeamBtn) {
        addTeamBtn.addEventListener("click", function() {
            let name = document.getElementById("team-name-input").value;
            let data = {
                name: name,
            };
            console.log(data);
            jQuery.post(
                `${websiteUrl}/wp-json/whatsapp/v1/teams`,
                data,
                function(data) {
                    alert(data.message);
                    window.location.reload();
                },///////////////////*********************************************************************************************************************-++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++------------------------------+ */
            );
        })
    }


    let membersArray = [];
    let membersIdArray = [];
    let memberSelect = document.getElementById("member");

    if (memberSelect) {

        let memberDiv = document.getElementById("members");

        function deletemember(id, id2) {
            membersArray.splice(membersArray.indexOf(id), 1);
            membersIdArray.splice(membersIdArray.indexOf(id2), 1);
            memberDiv.innerHTML = "";
            for (let i = 0; i < membersArray.length; i++) {
                memberDiv.innerHTML += `<div onclick="deletemember('${membersArray[i]}', '${membersIdArray[i]}')" class="member">${membersArray[i]}</div>`;
            }
        }
        memberSelect.addEventListener("change", function(e) {
            let memberId = e.target.selectedOptions[0].value;
            let memberValue = e.target.selectedOptions[0].text;

            if (!membersArray.includes(memberValue)) {
                membersArray.push(memberValue);
                membersIdArray.push(memberId);
            }

            memberDiv.innerHTML = "";
            for (let i = 0; i < membersArray.length; i++) {
                memberDiv.innerHTML += `<div onclick="deletemember('${membersArray[i]}', '${membersIdArray[i]}')" class="member">${membersArray[i]}</div>`;
            }
        })
    }
    else {
    }

    let addmemberBtn = document.getElementById("addMember");
    if (addmemberBtn) {
        addmemberBtn.addEventListener("click", function() {
            let teamId = document.getElementById("teamId").value;
            let data = {
                members: membersIdArray
            };
            jQuery.post(
                `${websiteUrl}/wp-json/whatsapp/v1/teams/${teamId}`, // corrected url syntax
                data, // corrected variable name from "date" to "data"
                function(response) { // corrected callback parameter name from "data" to "response"
                    alert(response.message);
                    window.location.reload();
                }
            ).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error sending POST request:", textStatus, errorThrown);
                console.error("Response:", jqXHR.responseText);
            });
        })
    }



    </script>
