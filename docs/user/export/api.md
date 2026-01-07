---
title: Export Data using the API
permalink: '/user/export/api/'
toc: false
---

Epigraf stores data according to the [Relational Article Model](/epigraf/user/coreconcepts/model).
The most straightforward method for mapping data to simple tables is using the [rpigraf-package](https://github.com/datavana/rpigraf/).
In the [example vignette](https://github.com/datavana/rpigraf/blob/main/vignettes/import.Rmd), you find code chunks for importing data and for getting it back from Epigraf.
Further, the package implements some useful features for data analysis such as showing annotated text segments.

This is how you fetch data from Epigraf:

``` plaintext
epi <- api_fetch("articles", db = "epi_movies")
```

The data comes in RAM format. The distill methods extract tidy tables:

``` plaintext
distill_articles(
  epi,

  # Get some article fields
  c("signature", "name"),

  # ...and property data used in its items
  item.type = "categories",
  property.cols = "lemma"

 )
```

Properties can be extracted including the annotated text segments:

``` plaintext
 distill_properties(epi, "annotations", annos = TRUE)
```
