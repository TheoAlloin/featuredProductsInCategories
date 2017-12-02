<fieldset>
    {if isset($confirmation)}
        <div class="alert alert-success">Informations mises à jour</div>
    {/if}
    {$categories_tree}
    <input type="submit" name="submitFeaturedProducts" type="submit" class="btn btn-default pull-right" />
</fieldset>
