---
title: Import Data Using the API
permalink: '/user/import/api/'
toc: false
---

Importing data into Epigraf requires mapping your data to the [Relational Article Model](/epigraf/user/coreconcepts/model).
The most straightforward method is preparing your data with R using the [rpigraf-package](https://github.com/datavana/rpigraf/).
In the [example vignette](https://github.com/datavana/rpigraf/blob/main/vignettes/import.Rmd), you find code chunks for importing data and for getting it back from Epigraf.
Further, the package implements some useful features for data analysis such as showing annotated text segments.

The following example transforms three cases from a table into the relational article model:

<figure class="table">
<table>
<thead>
<tr>
<th>Case</th>
<th>Title</th>
<th>Genre</th>
<th>Text</th>
</tr>
</thead>
<tbody>
<tr>
<td>011</td>
<td>Westworld</td>
<td>Western</td>
<td>In a futuristic amusement park, androids populate themed worlds like the Wild West.</td>
</tr>
<tr>
<td>012</td>
<td>Yellowstone</td>
<td>Western</td>
<td>Ranch owner John Dutton battles to protect his family's massive Montana cattle ranch.</td>
</tr>
<tr>
<td>013</td>
<td>Once Upon</td>
<td>Western</td>
<td>A mysterious harmonica-playing stranger teams up with a bandit.</td>
</tr>
</tbody>
</table>
</figure>

That's how the result looks like:

![Epigraf API](/epigraf/user/assets/img/import_api.png)


The craft functions either map existing columns or fill in values not
provided in the source table:

``` plaintext
ds <- ds |> craft_projects  (
  fill = c("type" = "default", "fragment" = "movies",  "name" = "Movies", "signature"="movies")
)
ds <- ds |> craft_articles  (
  fill = c("type" = "default"),
  cols = c("fragment" = "case", "signature" = "case", "name" = "title")
)
ds <- ds |> craft_sections  (
  fill = c("type" = "text", "name" = "Abstract")
)
ds <- ds |> craft_items (
  fill = c("type" = "text"),
  cols = c("content" = "text")
)
ds <- ds |> craft_sections(
  fill = c("type" = "categories", "name" = "Genres")
)
ds <- ds |> craft_properties(
  fill = c("type" = "categories"),
  cols = c("fragment"="genre", "lemma" = "genre")
)
ds <- ds |> craft_items(
  fill = c("type" = "categories"),
  cols = c("properties_id" = ".property")
)
```

The data is now ready to be compiled into the RAM format and to be
uploaded to the database:

``` plaintext
epi <- ram_compile(ds)
api_patch(epi, db = "epi_movies")
```
