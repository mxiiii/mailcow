{if isset($smarty.session['msg']) && is_array($smarty.session['msg'])}
	<div class="container">
		{foreach from=$smarty.session['msg'] key=key item=value}
			{if $smarty.session['msg'][$key]}
				<div class="alert alert-{$key}" role="alert">{$smarty.session['msg'][$key]}</div>
			{/if}
		{/foreach}
	</div>
{/if}