---
title: Export Data using the API
permalink: '/user/export/api/'
toc: false
---

Epigraf stores data according to the [Relational Article Model](/epigraf/user/coreconcepts/model).
You can retrieve it directly from the database, if you have direct access to the database server.
However, usually, it is more convenient to use the [Epigraf API](/epigraf/user/coreconcepts/api) for exporting data.
Once you have retrieved the data, you can convert it into tidy tables for further analysis.

The most straightforward method for mapping data to simple tables is using the [rpigraf-package](https://github.com/datavana/rpigraf/).
In the [example vignettes](https://github.com/datavana/rpigraf/blob/main/vignettes/),
you find code chunks for importing data and for getting it back from Epigraf.
Further, the package implements some useful features for data analysis such as showing annotated text segments.

There are two main entry points for fetching data from Epigraf:

- Articles: By retrieving articles, you can access all data used in the article,
  including its project, sections, items, linked properties, and annotations.
- Properties: You can also fetch properties directly.
  This is useful to get the full property tree.
  But note (by now), you cannot access article-related information this way.


# Articles

This is how you fetch full article data from Epigraf using the API:

```
epi <- api_fetch("articles", db = "epi_movies")
```

The data comes in RAM format. The distill methods extract tidy tables:

```
distill_articles(
  epi,

  # Get some article fields
  c("signature", "name"),

  # ...and property data used in its items
  item.type = "categories",
  property.cols = "lemma"

 )
```

Properties used in the fetched articles can be extracted including the annotated text segments:

```
 distill_properties(epi, "annotations", annos = TRUE)
```

# Properties

Use the same methods to fetch properties.
You always need to specify the property type you want to retrieve
in the query parameters:

```
epi <- api_fetch("properties", list(propertytype="roles"), db="epi_belong")
```

The result already contains all available property data as a flat table.
We suggest you inspect the resulting data frame. It includes, for example,
the parent_id and lft/rght values for reconstructing the property tree.

The distill function may be handy for hierarchical data
(if you have parent and child properties) because it adds a path column
that contains the full hierarchy of lemmas:

```
distill_properties(epi, "annotations")
```

Note that the API and the package are under continuous development.
We are working on adding article data to property fetches as well.
