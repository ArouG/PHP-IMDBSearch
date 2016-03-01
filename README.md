# PHP-IMDBSearch
find on IMDB.COM some titles like an input title

INPUT :

1 - a title ($_GET['t']),

2 - a country ($_GET['c']) : in order to filter all the titles of the 'movies' ('all' for no filter),

3 - a MoviesTypes ($_GET['g'] & see langages.php : $Types) : to filter only on some 'MoviesTypes'

OUTPUT :
  '0' if no title match the entry on IMDb.com, else an array:
  
  array(
  
		imdb_id   : something like tt0456201 <- Id IMDB of the movie
		title     : principal title (depend on country),
		year      : IMDB year of the movie,
		genres    : (string) IMDB genre(s),   [ATTENTION : in french but could be changed]
		directors : (string) IMDB director(s),
		poster    : (string / URL) the biggest IMAGE if exists,
		AKAs      : (string) IMDB also_known_as (depend too on INPUT country),
		video     : (string / URL) IMDB trailer if exists,
		type      : IMDB MoviesTypes,   [ATTENTION : in french but could be changed]
		pere      : IMDB_id for the serie if type=episode,
  )

adapted from https://github.com/abhinayrathore/PHP-IMDb-Scraper

TO DO : Try to deceive IMDB.com to persuade him whom we are in France / Italy / else !

ATTENTION : strings - in the output - are filtered on "commas" because trouble with Javascript JSON.parse 
see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/JSON/parse
