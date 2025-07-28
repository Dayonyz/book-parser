# Remote Book Parser

## Deploy

```
make generate-env
make build
make install
```

## Run Application

```
make start (if not started)
make ssh
```

## Parse Books

```
make ssh

If you want to parse invalid ISBN - remove 'isbn' validator from

app/Services/Parsers/BookEntryTransformer.php - 32 line

['isbn' => ['required', 'string', 'isbn']] >>>> ['isbn' => ['required', 'string']]

php artisan app:book-remote-parse
```

## All route list

```
php artisan route:list
```

## Get Author Books (for example)

```
http://localhost:8080/api/authors/1/books
```

# Technical requirements

### 1) Develop a json parser for the resource 
```
 "https://raw.githubusercontent.com/bvaughn/infinite-list-reflow-examples/refs/heads/master/books.json"
 Predict information updates based on the unique isbn field
```

### 2) Create endpoints that use the result from the first task:
```
List of books. 
    Response: 
        book title, 
        description, 
        list of authors, 
        publication date. 
    Search: 
        title, 
        description, 
        author (possible by author_id)
List of authors. 
    Response: 
        author name
        number of books. 
    Search: 
        author name
Author's books. 
    Response: 
        book title, 
        description, 
        list of authors, 
        publication date.

```


