<?php
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->addCollection(
    // second argument is the type, which is required to enable
    // the annotation reader for this resource
    $loader->import("@AppBundle/Controller/", "annotation")
);

return $collection;