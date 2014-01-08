<tr>
	<td>{$item.id}</td>
	<td>{$item.docid}</td>
	<td>{$item.state}</td>
	<td>{$item.sent_date|date_format:"%c"}</td>
	<td>{$item.total}</td>
	<td>{$item.sent}</td>
	<td>{$item.bounced}</td>
	<td>{$item.sender}</td>
	<td>{$item.sender_email}</td>
	<td>{$item.sender_auto}</td>
	<td>{$item.sender_priority}</td>
	<td>{$item.process_time} Sekunden</td>
	<td>{$item.create_time} Sekunden</td>
</tr>