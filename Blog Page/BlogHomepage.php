<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Homepage</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <header>
        <p>Welcome to our blog</p>
    </header>
    <div class="search-bar">
        <input type="text" id="searchBar" onkeyup="filterPosts()" placeholder="Search posts...">
    </div>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Your Complete Guide To Brewing</h5>
                <p class="card-text">
                    We constantly get asked by our valued customers the correct way to brew their bean. Thus we decided to make you a post with all the tips and tricks you need.
                </p>
                <a href="GuideToBrewing.php" class="btn-primary">Read More</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">The Scientifically Perfect Brew</h5>
                <p class="card-text">
                    Everyone brews their beans differently to suit their tastebuds, so let's take a look at what science says. We've summarized decades of research from all over the world into a simple post just for you.
                </p>
                <a href="ScientificBrewing.php" class="btn-primary">Read More</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Our Top Blends</h5>
                <p class="card-text">
                    Let's take a look at this year's favorite blends. Chosen by you!
                </p>
                <a href="FavouriteBlends.php" class="btn-primary">Read More</a>
            </div>
        </div>
    </div>
    <script>
        function filterPosts() {
            const input = document.getElementById('searchBar').value.toLowerCase();
            const cards = document.getElementsByClassName('card');
            for (let card of cards) {
                const title = card.querySelector('.card-title').innerText.toLowerCase();
                const description = card.querySelector('.card-text').innerText.toLowerCase();
                if (title.includes(input) || description.includes(input)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
