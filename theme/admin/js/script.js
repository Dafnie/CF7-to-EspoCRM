jQuery(document).ready(function($) {

    //get total static fields
    var parent_start = jQuery('.parent_static').length;
    var child_start = jQuery('.child_static').length;

	jQuery('.add_field').click(function (event) {
        event.preventDefault();
		
        var entity = jQuery(this).attr('data-id');
        var select = jQuery('.' + entity + ' select:last-of-type').html();
        if (entity == 'parent_') {
            parent_start++;
            entitynum = entity + parent_start;
        } else {
            child_start++;
            entitynum = entity + child_start;
        }

        jQuery('.' + entity + ' tr:last-of-type').parent()
          .append('<tr><td><input type="text" name="' + entitynum + '_static"> </td><td><select name="' + entitynum + '_static_espo">' + select + '</select></td><td>');

	});
});