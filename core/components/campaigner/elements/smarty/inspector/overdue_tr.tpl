<tr>
	<td>{$item.id}</td>
	<td>{$item.newsletter}</td>
	<td>{$item.state}</td>
	<td>{$item.error}</td>
	<td>{$item.created|date_format:"%c"}</td>
	<td>{$item.sent_date|date_format:"%c"}</td>
	<td>{$item.email}</td>
	<td>{$item.overdue}</td>
</tr>