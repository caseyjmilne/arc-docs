<?php 

function arc_docs()
{
    return Collection::get('docs');
}

function arc_get_doc($id)
{
    return arc_docs()->find($id);
}

function arc_create_doc($attributes)
{
    return arc_docs()->create($attributes);
}