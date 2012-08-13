<!-- template="postbox" -->
<div id="[+id+]" class="postbox" >
<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span>[+title+]</span></h3>
<div class="inside">
	[+inside_html+]
	</div>
</div>
<!-- template="page" -->
<form method="post" action="/wp-admin/upload.php?page=[+page+][+month+]" class="mla-display-single-item" id="mla-display-single-item-id">
<input type="hidden" id="type-of-[+ID+]" value="[+post_mime_type+]" />
<input type="hidden" name="attachments[[+ID+]][menu_order]" value="[+menu_order+]" />
<input type="hidden" name="mla_admin_action" value="[+mla_admin_action+]" />
<input type="hidden" name="mla_item_ID" value="[+ID+]" />
[+view_args+][+_wpnonce+]
<p class="submit" style="padding-bottom: 0;">
<input name="update" type="submit" class="button-primary" value="Update" />&nbsp;
<input name="cancel" type="submit" class="button-primary" value="Cancel" />
</p>
<div id="poststuff" class="metabox-holder has-right-sidebar">
<div class="mla-media-single">
<div id="media-item-[+ID+]" class="mla-media-item">
<table class="slidetoggle describe">
<thead class="media-item-info" id="media-head-[+ID+]">
<tr valign="top">
<td class="A1B1" id="thumbnail-head-[+ID+]">
<p>[+attachment_icon+]</p>
</td>
<td>
<p><strong>File name:</strong> [+file_name+]</p>
<p><strong>File type:</strong> [+post_mime_type+]</p>
<p><strong>Upload date:</strong> [+post_date+]</p>
</td>
</tr>
</thead>
<tbody>
<tr class="post_title form-required">
<th valign="top" scope="row" class="label"><label for="attachments[[+ID+]][post_title]"><span class="alignleft">Title</span>
<span class="alignright"><abbr title="required" class="required">*required</abbr></span><br class="clear" /></label></th>
<td class='field'><input type='text' class='text' id='attachments[[+ID+]][post_title]' name='attachments[[+ID+]][post_title]' value='[+post_title+]'  aria-required='true'  /></td>
</tr>
		<tr class='post_name form-required'>
			<th valign='top' scope='row' class='label'><label for='attachments[[+ID+]][post_name]'><span class='alignleft'>Name/Slug</span><span class="alignright"><abbr title="required" class="required">*required</abbr></span><br class='clear' /></label></th>
			<td class='field'><input type='text' class='text' id='attachments[[+ID+]][post_name]' name='attachments[[+ID+]][post_name]' value='[+post_name+]'  /><p class='help'>Must be unique; will be validated.</p></td>
		</tr>
		<tr class='post_excerpt'>
			<th valign='top' scope='row' class='label'><label for='attachments[[+ID+]][post_excerpt]'><span class='alignleft'>Caption</span><br class='clear' /></label></th>
			<td class='field'><input type='text' class='text' id='attachments[[+ID+]][post_excerpt]' name='attachments[[+ID+]][post_excerpt]' value='[+post_excerpt+]'  /></td>
		</tr>
		<tr class='post_content'>
			<th valign='top' scope='row' class='label'><label for='attachments[[+ID+]][post_content]'><span class='alignleft'>Description</span><br class='clear' /></label></th>
			<td class='field'><textarea id='attachments[[+ID+]][post_content]' name='attachments[[+ID+]][post_content]' >[+post_content+]</textarea></td>
		</tr>
		<tr class='parent_info'>
			<th valign='top' scope='row' class='label'><label for='attachments[[+ID+]][parent_info]'><span class='alignleft'>Parent Info</span><br class='clear' /></label></th>
			<td class='field'><table><tr><td style="width: 50px; vertical-align:top" ><input type='text' class='text' name='attachments[[+ID+]][post_parent]' value='[+post_parent+]' /></td><td><input type='text' class='text' readonly='readonly' name='attachments[[+ID+]][parent_info]' value='[+parent_info+]' /></td></tr><tr><td colspan="2"><p class='help'>ID, type and title of parent, if any.</p></td></tr></table></td>
		</tr>
		<tr class='image_url'>
			<th valign='top' scope='row' class='label'><label for='attachments[[+ID+]][image_url]'><span class='alignleft'>File URL</span><br class='clear' /></label></th>
			<td class='field'><input type='text' class='text urlfield' readonly='readonly' name='attachments[[+ID+]][url]' value='[+guid+]' /><br /><p class='help'>Location of the uploaded file.</p></td>
		</tr>
		<tr class='features'>
			<th valign='top' scope='row' class='label'><label for='attachments[[+ID+]][features]'><span class='alignleft'>Featured in</span><br class='clear' /></label></th>
			<td class='field'><textarea id='attachments[[+ID+]][features]' rows='5' readonly="readonly" name='attachments[[+ID+]][features]' >[+features+]</textarea></td>
		</tr>
		<tr class='inserts'>
			<th valign='top' scope='row' class='label'><label for='attachments[[+ID+]][inserts]'><span class='alignleft'>Inserted in</span><br class='clear' /></label></th>
			<td class='field'><textarea id='attachments[[+ID+]][inserts]' rows='5' readonly="readonly" name='attachments[[+ID+]][inserts]' >[+inserts+]</textarea></td>
		</tr>
</tbody>
</table> <!-- class="slidetoggle describe" -->
</div> <!-- class="media-item" -->
</div> <!-- class="media-single" -->
<div id="side-info-column" class="mla-inner-sidebar">
[+side_info_column+]
</div><!-- side-info-column -->
<br class="clear" />
</div><!-- /poststuff -->
</form>
