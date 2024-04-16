<?php
global $wpdb;
if (isset($_GET["member"]) && !empty($_GET["member"])) { 
    $id = $_GET["member"];
    $member = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "team_members WHERE id = $id");
    ?>
<h1 class="headline" >Edit Member</h1>
    <div class="form-group">
        <input type="hidden" id="memberId" value="<?php echo $id; ?>">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo $member->name; ?>">
        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" value="<?php echo $member->phone_number; ?>"></label>
        <button type="button" class="btnCreate" id="editMember">Edit</button>
        <button type="button" class="btnCreate" id="deleteMember">Delete</button>
    </div>
<?php 
}
else{
?>
<h1 class="headline" >Members</h1>
<div class='addMember'>
    <label for="member">Add New Member</label>
    <input type="text" id="member" name="member" placeholder="Member Name">
    <input type="text" id="phone" name="phone" placeholder="Phone Number">
    <button type="button" class="btnCreate" id="addMember">Add</button>
</div>
<div class="members" >
<table style="width: 100%; border-collapse: collapse; border-spacing: 0;">
    <thead style="background-color: #f2f2f2;">
    <tr>
        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Name</th>
        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Phone Number</th>
    </tr>
    </thead>
    <tbody id="members-table-body" style="background-color: #fff;">
    </tbody>
</table>
</div>
<?php }
?>

<script>
const websiteUrl = '<?php echo get_site_url(); ?>';  // Assuming script is within WordPress
jQuery(document).ready(function () {

    let tbody = jQuery("#members-table-body");
    if(tbody.length > 0){
        jQuery.get(
            websiteUrl + "/wp-json/whatsapp/v1/members",
            function (data) {
                tbody.append(
                    data.map((member) => {
                        return `<tr><td style="padding: 8px; border-bottom: 1px solid #ddd;"> <a href="${websiteUrl}/wp-admin/admin.php?page=whatsapp-members&member=${member.id}">${member.name}</a></td><td style="padding: 8px; border-bottom: 1px solid #ddd;">${member.phone_number}</td></tr>`;
                    }).join("")
                );
            },
            "json"
        );}
});

 let addBtn = jQuery("#addMember");
 if (addBtn) {
    addBtn.on("click", function() {
        let name = jQuery("#member").val();
        let phone = jQuery("#phone").val();
        let data = {
            name: name,
            phone: phone,
        };
        jQuery.post(
            websiteUrl+"/wp-json/whatsapp/v1/members",
            data,
            function(response) {
                console.log(response);
                alert(response.message);
                window.location.reload();
            }
        ).fail(function(error) {
            console.error(error);
        });
    });
}

let editBtn = jQuery("#editMember");
if (editBtn) {
    editBtn.on("click", function() {
        let name = jQuery("#name").val();
        let phone = jQuery("#phone").val();
        let id = jQuery("#memberId").val();
        console.log(id);
        let data = {
            name: name,
            phone: phone,
        };
        console.log(data);
        jQuery.post(
            websiteUrl+"/wp-json/whatsapp/v1/members/"+id,
            data,
            function(response) {
                console.log(response);
                alert(response.message);
                window.location.reload();
            }
        ).fail(function(error) {
            console.error(error);
        });
    })};

let deleteBtn = jQuery("#deleteMember");
if (deleteBtn) {
    deleteBtn.on("click", function() {
        let id = jQuery("#memberId").val();
        jQuery.ajax({
            url: websiteUrl+"/wp-json/whatsapp/v1/members/"+id,
            type: 'DELETE',
            success: function(response) {
                console.log(response);
                alert(response.message);
                window.location.href = websiteUrl+"/wp-admin/admin.php?page=whatsapp-members"; // remove id from url

            },
            error: function(error) {
                console.error(error);
            }
        });
    });}
</script>