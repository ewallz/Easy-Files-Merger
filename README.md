# Easy-Files-Merger
Easily merge all your project files content into a single text file for AI analysis purposes.

I've created this useful script to prepare the document for AI engine to analyse my projects. I could not find any online tools that can use AI to analyse the whole project codes on large scales, like dropping the whole project folder and let the AI analyse & suggest improvements. 

So i figured out a way to feed the AI with all the codes available within my project repo. We can actually upload project files one by one but its too time consuming if you have so many files/folders. So why not we put all the codes in a single txt file, and feed the AI with this single txt file instead? Will be much easier, right?

# How to Use?
1. Place the combiner.php file in your root of your project folder. E.g for laravel, put it in the same dir as the .env file.
2. Run the script via cmd: php combiner.php
3. It traverse your project directory > generate directory tree > copy all the codes in all the files > create headers for each file > merges all tof the codes in a single txt file called combined.txt.
4. The generated combined.txt file will be available in the same folder as the combiner.php
5. Feed/Upload the combined.txt file into your prefered AI engine along with suitable prompt for analysis.

# Buit-in Features
1. Directory Tree Generator - It's important to make the AI understand our project structure. So I include the directory tree generator which will generate a nice visual of the project structure at the very beginning of combined.txt file.
2. Exclude/Include folders/files/extensions.
3. Progress indicator.

# Use Case
I'm using this mostly with Google Gemini AI since this is the only engine that support 1M token chat context. This will keep your conversation to use the same data from the uploaded combined.txt file without loosing any context.

# Configurable
// Specify output filename
`$outputFile = "$rootDirectory/combined.txt";`

// Specify the file extensions to include
`$allowedExtensions = ['php', 'js', 'css', 'html', 'json', 'lock'];`

// Specify the directories and filenames to ignore
`$ignoredDirectories = ['vendor', 'node_modules', 'storage', 'bootstrap/cache', '.git', 'tests'];
$ignoredFilenames = ['combiner.php', 'run.php', 'combined.txt'];`


If this script did help you, please consider to buy me a coffee :-)


![Static Badge](https://img.shields.io/badge/buyme-coffee-brightgreen) https://sociabuzz.com/ewallzdev/donate

