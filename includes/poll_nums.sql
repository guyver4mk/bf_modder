SELECT 
  (
    (SELECT count(selected) FROM brave_frontier.poll_next WHERE selected='unit_chk')
  ) as units,
  ( 
        (SELECT count(selected) FROM brave_frontier.poll_next WHERE selected='evo_mats')
  ) as evo,
  (
    (SELECT count(selected) FROM brave_frontier.poll_next WHERE selected='item_mats')
  )as item,
  (
    (SELECT count(selected) FROM brave_frontier.poll_next WHERE selected='quests')
  ) as quest,
  (
    (SELECT count(selected) FROM brave_frontier.poll_next WHERE selected='other')
  )  as other
from brave_frontier.poll_next
GROUP BY units, evo,item, quest, other
LIMIT 1;
