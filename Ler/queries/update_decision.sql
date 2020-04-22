INSERT INTO Decisions (id, content, parent_id, next_arc_id)
VALUES(:decision_id, :content, :parent_arc_id, :next_arc_id)
ON DUPLICATE KEY
UPDATE next_arc_id=:next_arc_id, content=:content