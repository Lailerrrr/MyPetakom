 <?php
function calculateMerit($eventLevel, $position) {
    $meritTable = [
        'International' => [
            'Main committee' => 100,
            'Committee' => 70,
            'Participant' => 50
        ],
        'National' => [
            'Main committee' => 80,
            'Committee' => 50,
            'Participant' => 40
        ],
        'State' => [
            'Main committee' => 60,
            'Committee' => 40,
            'Participant' => 30
        ],
        'District' => [
            'Main committee' => 40,
            'Committee' => 30,
            'Participant' => 20
        ],
        'UMPSA' => [
            'Main committee' => 30,
            'Committee' => 20,
            'Participant' => 5
        ]
    ];

    if (isset($meritTable[$eventLevel][$role])) {
        return $meritTable[$eventLevel][$role];
    } else {
        return 0; // default if invalid
    }
}
?>
