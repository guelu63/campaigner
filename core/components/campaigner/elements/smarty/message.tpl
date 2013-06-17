<tr>
	<td>{$message.sent_date|date_format:"%Y-%m-%d %H:%M:%S"}</td>
	<td>{$message.subject}</td>
	<td>{$message.recipient}</td>
	<td>{$message.action}</td>
	<td>{$message.status}</td>
	<td>{if $message.success === true}
			<img src="{$path}manager/templates/default/images/modx-theme/dd/drop-yes.gif" />
		{else}
			<img src="{$path}manager/templates/default/images/modx-theme/dd/drop-no.gif" />
		{/if}
	</td>
</tr>