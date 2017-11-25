{if $error}
    {$error|var_dump}
    <div class="alert alert-danger">{$error}</div>
{/if}
<form action ="{$submitFormRouting}" method="post" enctype="multipart/form-data">
    {$categories_tree}
    <input type="hidden" name="current_product" value="{$current_product}" />
    <input id="currentUrl" type="hidden" name="currentUrl" value="{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" />
    <button type="submit" name="submitFeaturedProducts" class="btn btn-default pull-right"><i class="process-icon-save"></i> Enregistrer</button>
</form>
