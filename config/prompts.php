<?php

declare(strict_types=1);

/**
 * Conversation prompt packs.
 *
 * Prompts are conversation sparks — gentle, topic-based questions that appear
 * optionally inside rooms when the user has them enabled. They are never
 * forced, never streak-based, and never used as engagement bait.
 *
 * Each pack is designed to support ~100 prompts; V1 seeds 10–20 per pack.
 *
 * supporter_only: false  → available to all users (the 'general' pack)
 * supporter_only: true   → requires supporter status
 */
return [

    'packs' => [

        // ── Free pack ────────────────────────────────────────────────────────

        'general' => [
            'label'          => 'General',
            'description'    => 'Friendly prompts for any conversation.',
            'supporter_only' => false,
            'prompts'        => [
                "What's something interesting you've been thinking about lately?",
                'If you could learn any skill instantly, what would it be?',
                "What's something small that made you happy recently?",
                "What's a show or movie you'd recommend to pretty much anyone?",
                "What hobby have you always wanted to try but haven't yet?",
                "What's something you're looking forward to?",
                'If you could live anywhere for a year, where would you go?',
                "What's a skill you're proud of?",
                "What kind of music puts you in a good mood?",
                "What's a book that stuck with you?",
                'If you could have any job for a day, what would it be?',
                "What's your comfort food?",
                "What's something you learned recently that surprised you?",
                "What's a small thing that makes everyday life better?",
                "What game, book, or show do you keep recommending?",
            ],
        ],

        // ── Supporter packs ───────────────────────────────────────────────────

        'fantasy_dnd' => [
            'label'          => 'Fantasy / D&D',
            'description'    => 'Wizards, taverns, and fictional worlds.',
            'supporter_only' => true,
            'prompts'        => [
                'What class do you always end up playing?',
                'What fictional tavern would you actually visit?',
                "What's your favorite cozy fantasy setting?",
                'What magic item would make your life easier?',
                'What fictional world feels like home?',
                "What's your ideal fantasy party composition?",
                'What D&D alignment are you, honestly?',
                "What's the most underrated fantasy creature?",
                "What's your go-to character backstory?",
                "What's a fantasy food you wish was real?",
                'What fictional magic system do you find most interesting?',
                "What's a cozy fantasy game you always come back to?",
            ],
        ],

        'cozy_gaming' => [
            'label'          => 'Cozy Gaming',
            'description'    => 'Games, vibes, and virtual homes.',
            'supporter_only' => true,
            'prompts'        => [
                "What's your comfort game when you just need to decompress?",
                'What game world would you actually want to live in?',
                "What's the best game soundtrack for background studying?",
                "What's your most hours in one game?",
                'What game made you feel genuinely emotional?',
                "What cozy game would you recommend to someone new to gaming?",
                "What's a game mechanic you wish more games had?",
                'What NPC would you most want as a roommate?',
                "What game has the best overall vibe?",
                "What's your current game rotation?",
                'What unfinished game do you keep meaning to go back to?',
                'What game would you recommend to someone who says they hate games?',
            ],
        ],

        'books_stories' => [
            'label'          => 'Books & Stories',
            'description'    => 'Reading, storytelling, and fictional worlds.',
            'supporter_only' => true,
            'prompts'        => [
                "What's a book you think about even though you finished it ages ago?",
                'What genre do you always come back to?',
                "What fictional world would you want to live in?",
                "What's a book you always want to recommend but it's hard to describe?",
                "What kind of ending do you prefer — happy, bittersweet, or open?",
                "What's a book you read too young and want to reread now?",
                'What author do you wish wrote more?',
                "What's your reading setup like?",
                "What's the last thing that made you emotional in a book?",
                "What's a short story you'd recommend?",
                "What book changed how you think about something?",
                "What's your favorite unreliable narrator?",
            ],
        ],

        'sci_fi' => [
            'label'          => 'Sci-Fi',
            'description'    => 'Future tech, space, and big ideas.',
            'supporter_only' => true,
            'prompts'        => [
                'What sci-fi tech would you most want to exist right now?',
                'What space colony would you sign up for?',
                "What's your favorite quiet sci-fi — no explosions, just ideas?",
                'What sci-fi creature companion would you want?',
                "What's your favorite fictional spaceship?",
                'What author changed how you think about the future?',
                'What sci-fi concept do you find most genuinely unsettling?',
                "What's the most optimistic sci-fi story you know?",
                'What sci-fi technology do you think is closest to existing?',
                "What's a sci-fi world that seemed utopian but wasn't?",
                'What near-future story feels most realistic to you?',
                "What's your favorite first contact story?",
            ],
        ],

        'music_discovery' => [
            'label'          => 'Music Discovery',
            'description'    => 'Songs, albums, and sonic rabbit holes.',
            'supporter_only' => true,
            'prompts'        => [
                "What's a song you'd put on any playlist?",
                'What album do you always come back to?',
                'What artist are you listening to lately that not many people know?',
                "What's the best song for driving late at night?",
                'What genre do you explore when you want something different?',
                "What's a song that instantly changes your mood?",
                "What's your comfort album?",
                "What's a song that would surprise people who know your usual taste?",
                "What's your focus music?",
                'What concert do you wish you could have attended?',
                'What song do you have on repeat right now?',
                'What genre did you discover late that you wished you found earlier?',
            ],
        ],

        'creative_projects' => [
            'label'          => 'Creative Projects',
            'description'    => 'Making things, learning things, building things.',
            'supporter_only' => true,
            'prompts'        => [
                'What are you currently working on or wishing you could work on?',
                'What creative medium would you try if time was no issue?',
                "What project are you most proud of?",
                'What skill would make your creative work easier?',
                'What creative person inspires you right now?',
                "What's something you made that you want to make again but better?",
                "What's a project you keep starting and not finishing?",
                "What's something you're learning to make?",
                'What creative collaboration would you love to have?',
                'What does your creative workspace look like?',
                "What's the most unexpected thing you've made?",
                'What would you make if you knew no one would judge it?',
            ],
        ],

        'deep_talks' => [
            'label'          => 'Deep Talks',
            'description'    => 'Questions worth sitting with for a while.',
            'supporter_only' => true,
            'prompts'        => [
                "What belief have you genuinely changed your mind on?",
                "What's something you understand better now than five years ago?",
                "What does 'home' mean to you?",
                "What's a question you come back to often?",
                'What would your ideal day actually look like?',
                "What's something you're working on becoming?",
                'What do you think makes a friendship last?',
                "What's something you've accepted that you used to resist?",
                "What's the most useful thing someone has ever said to you?",
                "What do you think is underrated as a way to live?",
                "What's a value you hold that most people around you don't?",
                'What would you want to be remembered for?',
            ],
        ],

        'quiet_introvert' => [
            'label'          => 'Quiet Introvert',
            'description'    => 'Recharging, solo rituals, and the good kind of quiet.',
            'supporter_only' => true,
            'prompts'        => [
                "What's your ideal way to recharge after a long week?",
                "What's your favorite solo activity that makes you feel like yourself?",
                "What's the best kind of quiet?",
                'What does your perfect stay-in evening look like?',
                "What's a thing you do alone that you love?",
                'What hobby feels most like rest?',
                "What's your wind-down routine?",
                "What space makes you feel most at ease?",
                "What's a small ritual that makes your day better?",
                "What's something you enjoy that requires no other people?",
                "What's your recharge time like?",
                'What environment helps you think best?',
            ],
        ],

        'retro_internet' => [
            'label'          => 'Retro Internet',
            'description'    => 'The old web, early communities, dial-up nostalgia.',
            'supporter_only' => true,
            'prompts'        => [
                "What was your first internet memory?",
                'What forum or community shaped who you are online?',
                "What's a piece of early internet culture you miss?",
                'What website do you wish still existed?',
                "What web era do you think had the best aesthetic?",
                'What old internet skill do you still use?',
                'What early online community were you part of?',
                'What early game or app ate the most of your time?',
                "What username did you use everywhere back then?",
                "What's the most chaotic thing you witnessed on the early internet?",
                'What was your first social media or forum?',
                "What do you miss about the slower internet?",
            ],
        ],

        'horror_cozy' => [
            'label'          => 'Horror but Cozy',
            'description'    => 'Horror for people who like the vibe more than the scare.',
            'supporter_only' => true,
            'prompts'        => [
                'What horror movie would you rewatch just for the atmosphere?',
                "What's your favorite horror subgenre?",
                "What makes a horror setting feel 'cozy' to you?",
                "What's a horror recommendation that isn't actually that scary?",
                "What's your comfort horror movie?",
                'What horror creature do you find most interesting?',
                "What horror game has the best atmosphere?",
                "What's a horror story that made you think more than it scared you?",
                "What's the best autumn horror watch?",
                'What horror setting would you visit if it was safe?',
                "What's a horror movie with an underrated ending?",
                'What horror director do you think deserves more attention?',
            ],
        ],

    ],

];
