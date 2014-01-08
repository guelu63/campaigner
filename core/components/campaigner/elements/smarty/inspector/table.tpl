<h3>{$topic|upper} ({$count})</h3>
<p><strong>{$message}</strong></p>
<p><a href="{$link}">Link zu {$topic}</a><br/><small>{$link_description}</small></p>
<table class="{$type}" width="750" style="text-align:left">
	<thead>
	{section name=key loop=$keys}
  	<th>{$keys[key]}</th>
	{/section}
	</thead>
	<tbody>{$content}</tbody>
</table>
<hr/>