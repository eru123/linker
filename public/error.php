<?php $page_link = $_SERVER['REQUEST_URI'] ?? $_SERVER['SERVER_NAME'] ?? "Failed to retrieve page link";?>
<!DOCTYPE html>
<html>
<head>
  <title>The page you were looking for doesn't exist (404)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
  body {
    background-color: #fff;
    color: #2E2F30;
    font-family: sans-serif;
    margin: 0;
  }

  footer {
    position: absolute;
    bottom: 1%;
    width: 100%;
    text-align: center;
    font-size: .6em;
    color: #393939
  }

  a {
    color: #393939;
  }

  a:hover {
    color: #393939;
  }

  .dialog {
    float:right;
    text-align: left;
    width: 45%;
    margin: 5% auto 0;
  }

  h1 { 
    font-size: 5em;
    color: #393939;
    line-height: 1.5em;
  }

  p {
    font-size: 1.4em;
    color: #393939;
    padding-right: 5%;
  }
  .image {
    float: left;
    width:50%;
    margin: 5% 0 0 5%;

  }
  @media only screen and (max-width: 767px) {
    .image {
      width: 90%;
      margin: 5% 0 0 5%;
    }
    .dialog {
    float:none;
    text-align: center;
    width: 90%;
  }
  }  
  </style>
</head>

<body>
  <div>
    <div class="dialog">
      <h1>Whoops!</h1>
      <p>Clumsy us, we were unable to find the page you were looking for</p>
      <p><b>Page: </b><small><?php echo htmlentities($page_link); ?></small></p>
    </div>
    <img src="/img/groceries.png" class="image">
  </div>  
</body>
</html>
