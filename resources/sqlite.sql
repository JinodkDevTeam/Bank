-- #! sqlite
-- #{ bank
-- #    { init
CREATE TABLE IF NOT EXISTS Bank (
    Player VARCHAR(40) NOT NULL,
    Money FLOAT NOT NULL DEFAULT 0
);
-- #    }
-- #    { register
-- #        :player string
-- #        :value float
INSERT OR REPLACE INTO Bank (Player, Money)
VALUES (:player, :value);
-- #    }
-- #    { get
-- #        :player string
SELECT Money FROM Bank WHERE Player = :player;
-- #    }
-- #    { getall
SELECT * FROM Bank;
-- #    }
-- #    { remove
-- #        :player string
DELETE FROM Bank WHERE Player = :player;
-- #    }
-- #    { update
-- #        :player string
-- #        :value float
UPDATE Bank SET Money = :value WHERE Player = :player;
-- #    }
-- #    { top
SELECT * FROM Bank ORDER BY Money;
-- #    }
-- #}