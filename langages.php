<?php
global $Types;
       $Types[0]=Array('Movie', 'TV Movie', 'TV Series', 'TV Episode', 'TV Special', 'Mini-Series', 'Documentary', 'Short', 'Video');
       $Types[1]=Array('Film', 'Film TV', 'Série TV', 'Épisode TV', 'Émission TV', 'Mini série', 'Documentaire', 'Court métrage', 'Vidéo');
global $ITypes;
       $ITypes= array(  'Movie'       => 0,
                        'TV Movie'    => 1,
                        'TV Series'   => 2,
                        'TV Episode'  => 3,
                        'TV Special'  => 4,
                        'Mini-Series' => 5,
                        'Documentary' => 6,
                        'Short'       => 7,
                        'Video'       => 8 );

global $Imdb_Genres;
       $Imdb_genres[0]=Array('Action','Adventure','Animation','Biography','Comedy','Crime','Documentary','Drama',
                             'Family','Fantasy','Film-Noir','History','Horror','Music','Musical','Mystery',
                             'Romance','Sci-Fi','Sport','Thriller','War','Western');   
       $Imdb_genres[1]=Array('Action','Aventure','Dessin-animé','Biographie','Comédie','Criminel','Documentaire','Dramatique',
                             'Famille','Fantastique','Film-Noir','Historique','Horreur','Musique','Musical','Mystère',
                             'Romantique','Science-fiction','Sport','Thriller','Guerre','Western'); 
global $IImdb_genres;
       $IImdb_genres= array('Action'      => 0,
                            'Adventure'   => 1,
                            'Animation'   => 2,
                            'Biography'   => 3,
                            'Comedy'      => 4,
                            'Crime'       => 5,
                            'Documentary' => 6,
                            'Drama'       => 7,
                            'Family'      => 8,
                            'Fantasy'     => 9,
                            'Film-Noir'   => 10,
                            'History'     => 11,
                            'Horror'      => 12,
                            'Music'       => 13,
                            'Musical'     => 14,
                            'Mystery'     => 15,
                            'Romance'     => 16,
                            'Sci-Fi'      => 17,
                            'Sport'       => 18,
                            'Thriller'    => 19,
                            'War'         => 20,
                            'Western'     => 21);                                  
global $Countries;
        $Countries[0]=array("All", "Argentina", "Australia", "Austria", "Belgium", "Brazil", "Bulgaria", "Canada", "China", "Colombia", "Costa Rica", "Croatia", "Czech Republic", 
                                   "Denmark", "Dominican Republic", "Estonia", "Finland", "Germany", "Greece", "Hong Kong", "Hungary", "Iceland", "India", "Iran", "Ireland", "Italy", "Japan", 
                                   "Malaysia", "Mexico", "Netherlands", "New Zealand", "Norway", "Peru", "Pakistan", "Poland", "Portugal", "Romania", "Russia", "Serbia", "Slovakia", 
                                   "Slovenia", "Singapore", "South Africa", "South Korea", "Spain", "Sweden", "Switzerland", "Thailand", "Turkey", "Uruguay", "West Germany");
        // to be continued ^^
        $Countries[1]=array("Tous", "Argentine", "Australie", "Autriche", "Belgique", "Brésil", "Bulgarie", "Canada", "Chine", "Colombie", "Costa Rica", "Croatie", "République tchèque",
                                    "Danemark", "République Dominicaine", "Estonie", "Finlande", "Allemagne", "Grèce", "Hong Kong", "Hongrie",  "Islande", "Inde", "Iran", "Irelande", "Italie", "Japon",
                                    "Malaisie", "Mexique", "Pays Bas", "Nouvelle Zélande", "Norvège", "Pérou", "Paquistan", "Pologne", "Portugal", "Roumanie", "Russie", "Serbie", "Slovaquie",
                                    "Slovénie", "Singapour", "Afrique du Sud", "Corée du Sud", "Espagne", "Suède", "Suisse", "Thaïlande", "Turquie", "Uruguay", "Allemagne de l'Ouest");

global $IMDb_langages;
       $IMDb_langages[0] = array("Arabic", "Bulgarian", "Chinese", "Croatian", "Dutch", "English", "Finnish", "French",
                              "German", "Greek", "Hebrew", "Hindi", "Hungarian", "Icelandic", "Italian", "Japanese",
                              "Korean", "Norwegian", "Persian", "Polish", "Portuguese", "Punjabi", "Romanian", 
                              "Russian", "Spanish", "Swedish", "Turkish", "Ukrainian");       
?>