# Multi-Purpose-PHP-Query-Class
A PHP Class For Reducing The Number Of Lines Of Codes I Write Regarding Database Queries, Useful For Any PHP Project


It pretty simple to use, as it is just a class that gets initialized like any other class. i.e 
```
$query = new DbQuery();
```


** Please Modify The Construct Method, by handcoding/changing the dbname, username and password before usage. **


The Syntax Varies A Little, Depending On What Method You Want To Use.

# For Insert Queries:
```
$query->insert($whatTable, $whichColumns, $whatValues);
```

e.g
```
$query->insert("users", ['uniqid', 'username', 'email', 'password'], [$uniqid, $_POST['username'],$_POST['email'],md5($_POST['password'])]);
```

# For select Queries:
The Default (No Join In The SQL Statement):
```
select($selectWhat, $FromWhichTable,$joinArray = null, $WhereXEqualsY);
```
e.g
```
$query->select(["email","password",'uniqId'],"users", null, ["email" => $_POST['email']]);
```

For JOIN select Queries:
```
$query->select["username","users",["inner/left/right", "books ON users.id = books.userId", "debt ON users.id = debt.userId"], ["users.username => "shawn", "books.price => "2500"]]
```

# For delete Queries:
```
$query->delete($fromWhatTable, $WhereXEqualsY);
```
e.g
```
$query->delete(['users', ['age'=> '20', 'name' => 'shawn']])
```
# For Update Queries:

update($updateWhatTable,$setWhatToWhat, $WhereXEqualsY);
e.g
```
$query->update(["user", ['age' => '12', 'name'=> 'tami'], ['code' => '123', 'gender' => 'male']"])
```

** Pleas Note That Each Query Method Returns The Following: **

###### Insert/Update/Delete -> Bool(True/False) Or String("Duplicate" or Some Other PDO Error).
So if you do:
```
echo $query->Insert/Update/Delete(Whatever), it would return True/False/String.
```

###### Select -> Returns An Array if true and the fetched result -> [true, $result], Bool(False) Or String(PDO Error).
So if you do 

```
echo $query->Select(Whatever), if true would return an array(Which You Obviously cant see via echo, unless you did:
echo $query->Select(Whatever)[0] -> Which Would return True, If True.
$array = $query->Select(Whatever)[1] Would give you an array of data fetched from your database.
```

 
